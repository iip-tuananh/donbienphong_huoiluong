<?php

namespace App\Model\Admin;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use App\Model\Uptek\G7Info;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class PostCategory extends BaseModel
{

    use Sluggable;
    use SluggableScopeHelpers;

    public const TYPE_POST = 1;
    public const TYPE_PROJECT = 2;
    public const TYPE_SERVICE = 3;
    public const TYPE_KIENTHUC = 4;

    public const TYPE_ABOUT = 5;
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $table = 'post_categories';

    public CONST HOAT_DONG = 1;
    public CONST HUY = 0;

    public CONST STATUSES = [
        [
            'id' => self::HOAT_DONG,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => self::HUY,
            'name' => 'Hủy',
            'type' => 'danger'
        ],
    ];

    public static function getAll()
    {
        return self::orderby('sort_order')->get();
    }

    public function getChilds()
    {
        return self::with(['image'])->where('parent_id', $this->id)->orderBy('sort_order','asc')->get();
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getParent()
    {
        return self::with(['image'])->where('id', $this->parent_id)->first() ? self::where('id', $this->parent_id)->first() : null;
    }

    public function parentSlug()
    {
        return self::where('id', $this->parent_id)->first() ? self::where('id', $this->parent_id)->first()->slug : null;
    }

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function posts()
    {
        return $this->hasMany('App\Model\Admin\Post','cate_id','id')->orderBy('created_at','desc');
    }

    public function knowledge()
    {
        return $this->hasMany('App\Model\Admin\Post','cate_id','id')
            ->where('type', 'knowledge')
            ->orderBy('created_at','desc');
    }

    public function projects()
    {
        return $this->hasMany(Project::class,'cate_id','id')->orderBy('created_at','desc');
    }

    public function services()
    {
        return $this->hasMany(Service::class,'cate_id','id')->orderBy('updated_at','desc');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class,'cate_id','id')->orderBy('created_at','desc');
    }

    public function canEdit()
    {
        return $this->created_by == Auth::user()->id;
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

    public function canDelete ()
    {
        return Auth::user()->id == $this->created_by && $this->posts()->count() == 0 && $this->getChilds()->isEmpty();
    }

    public static function getForSelect($getParent = false) {
        $all = self::select(['id', 'name','sort_order','level', 'parent_id'])
            ->orderBy('sort_order', 'asc');

        if($getParent) {
            $all = $all->where(function ($query) {
                $query->where('level', 0);
            });
        }

        $all = $all->get()->toArray();

        $result = [];
        $result = array_map(function ($value) {
            if($value['level'] == 1) {
                $value['name'] = ' |-- '. $value['name'];
            }
            if($value['level'] == 2) {
                $value['name'] = ' |-- |-- '. $value['name'];
            }
            if($value['level'] == 3) {
                $value['name'] = ' |-- |-- |-- '. $value['name'];
            }
            if($value['level'] == 4) {
                $value['name'] = ' |-- |-- |-- | --'. $value['name'];
            }
            return $value;
        }, $all);
        return $result;
    }

    public static function getAllForEdit($id) {
        $all = self::where('id','<>',$id)
            ->where('parent_id','<>',$id)
            ->select(['id', 'name','sort_order','level'])
            ->orderBy('sort_order', 'asc')
            ->get()->toArray();
        $result = [];
        $result = array_map(function ($value) {
            if($value['level'] == 1) {
                $value['name'] = ' |-- '. $value['name'];
            }
            if($value['level'] == 2) {
                $value['name'] = ' |-- |-- '. $value['name'];
            }
            if($value['level'] == 3) {
                $value['name'] = ' |-- |-- |-- '. $value['name'];
            }
            if($value['level'] == 4) {
                $value['name'] = ' |-- |-- |-- | --'. $value['name'];
            }
            return $value;
        }, $all);
        return $result;
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'image'
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
        ->with([
            'customer',
            'g7Info',
            'user_create'
        ])
            ->firstOrFail();
    }

    public function send()
    {
        foreach($this->g7Info->users as $user) {
            $notification = new Notification();
            $notification->url = route("Post.show", $this->id, false);
            $notification->content = "Có đặt lịch mới từ hệ thống G7 Autocare";
            $notification->status = 0;
            $notification->receiver_id = $user->id;
            $notification->created_by = Auth::user()->id;
            $notification->save();
            $notification->send();
        }
    }

}
