<?php

namespace App\Model\Admin;

use App\Helpers\FileHelper;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Blog extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $dates = ['created_at', 'updated_at'];
    protected $appends = ['parent_category'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public const XUAT_BAN = 1;
    public const LUU_NHAP = 2;

    public const STATUSES = [
        [
            'id' => self::XUAT_BAN,
            'name' => 'Xuất bản',
            'type' => 'success'
        ],
        [
            'id' => self::LUU_NHAP,
            'name' => 'Lưu nháp',
            'type' => 'danger'
        ],
    ];

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function image_back()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_back');
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'cate_id', 'id');
    }

    public function galleries()
    {
        return $this->hasMany(RoomGallery::class, 'room_id', 'id');
    }
    public function getParentCategoryAttribute()
    {
        if ($this->category) {
            if ($this->category->parent_id == 0) {
                return null;
            }

            return $this->category->parent;
        }
        return null;
    }

    public function category_specials()
    {
        return $this->belongsToMany(CategorySpecial::class,'tour_category_special', 'tour_id', 'category_special_id');
    }

    public static function searchByFilter($request, $type = null)
    {
        $result = self::with([
            'user',
            'image'
        ]);

        if($type) {
            $result->where('type', $type);
        }

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        if (!empty($request->user_id)) {
            $result = $result->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        $post = self::where('id', $id)
            ->with([
                'galleries' => function ($q) {
                    $q->select(['id', 'room_id', 'sort'])
                        ->with(['image'])
                        ->orderBy('sort', 'ASC');
                },
                'image','image_back'
            ])
            ->firstOrFail();

        $post->category_special_ids = $post->category_specials->pluck('id')->toArray();

        return $post;
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->with([
                'image'
            ])
            ->firstOrFail();
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

    public function send()
    {
        foreach (User::all() as $user) {
            $notification = new Notification();
            $notification->url = route("Post.show", $this->id, false);
            $notification->content = Auth::user()->name . " vừa đăng bài viết mới <b>" . $this->name . "</b>";
            $notification->status = 0;
            $notification->receiver_id = $user->id;
            $notification->created_by = Auth::user()->id;
            $notification->save();

            $notification->send();
        }
    }

    public function getRelate() {
        return self::query()->whereNotIn('id', [$this->id])->get();
    }

    public function syncGalleries($galleries)
    {
        if ($galleries) {
            $exist_ids = [];
            foreach ($galleries as $g) {
                if (isset($g['id'])) array_push($exist_ids, $g['id']);
            }
            $deleted = RoomGallery::where('room_id', $this->id)->whereNotIn('id', $exist_ids)->get();
            foreach ($deleted as $item) {
                if ($item->image) {
                    FileHelper::deleteFileFromCloudflare($item->image, $item->id, RoomGallery::class);
                }
                $item->removeFromDB();
            }

            for ($i = 0; $i < count($galleries); $i++) {
                $g = $galleries[$i];

                if (isset($g['id'])) $gallery = RoomGallery::find($g['id']);
                else $gallery = new RoomGallery();

                $gallery->room_id = $this->id;
                $gallery->sort = $i;
                $gallery->save();

                if (isset($g['image'])) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, RoomGallery::class);
                        $gallery->image->removeFromDB();
                    }
                    $file = $g['image'];
                    // FileHelper::uploadFile($file, 'product_gallery', $gallery->id, ProductGallery::class, null, 99);
                    FileHelper::uploadFileToCloudflare($file, $gallery->id, RoomGallery::class, null);
                }
            }
        }
    }

}
