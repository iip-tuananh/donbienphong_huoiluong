<?php

namespace App\Model\Admin;

use App\Services\CategoryService;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;


class Category extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'categories';

    protected $fillable = ['id', 'name', 'slug', 'short_des', 'type', 'parent_id', 'intro', 'show_home_page', 'order_number'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public const HOAT_DONG = 1;
    public const HUY = 0;

    public const STATUSES = [
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

    public function getChilds()
    {
        return self::with(['image', 'products'])->where('parent_id', $this->id)
            ->where('level', 1)
            ->orderBy('sort_order', 'asc')->get();
    }

    public function getParent()
    {
        return self::where('id', $this->parent_id)->first() ? self::with(['image'])->where('id', $this->parent_id)->first() : null;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(self::class, 'parent_id')->where('level', 1)->orderBy('sort_order');
    }

    public function tours() {
        return $this->hasMany(Tour::class,'cate_id');
    }

    public function parentSlug()
    {
        return self::where('id', $this->parent_id)->first() ? self::where('id', $this->parent_id)->first()->slug : null;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function banner()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'banner');
    }

    public function products()
    {
        return $this->hasMany('App\Model\Admin\Product', 'cate_id', 'id')->orderBy('created_at', 'desc');
    }

//    public function manufacturers()
//    {
//        return $this->hasMany(Manufacturer::class, 'category_id');
//    }

    public function scopeParent($query) {
        return $query->where('parent_id', 0);
    }
    public function canEdit()
    {
        return $this->created_by == Auth::user()->id;
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

    public function canDelete()
    {
        return Auth::user()->id == $this->created_by && $this->products->count() == 0 && $this->getChilds()->isEmpty();
    }


    public static function getAll()
    {
        return self::orderby('sort_order')->get();
    }

    public static function getForSelect()
    {
        $all = self::select(['id', 'name', 'sort_order', 'level'])
            ->orderBy('sort_order', 'asc')
            ->get()->toArray();
        $result = [];
        $result = array_map(function ($value) {
            if ($value['level'] == 1) {
                $value['name'] = ' |-- ' . $value['name'];
            }
            if ($value['level'] == 2) {
                $value['name'] = ' |-- |-- ' . $value['name'];
            }
            if ($value['level'] == 3) {
                $value['name'] = ' |-- |-- |-- ' . $value['name'];
            }
            if ($value['level'] == 4) {
                $value['name'] = ' |-- |-- |-- | --' . $value['name'];
            }
            return $value;
        }, $all);
        return $result;
    }

    public static function getForSelect2()
    {
        return self::select(['id', 'name', 'sort_order', 'level'])
            ->where('parent_id', 0)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function getAllForEdit($id)
    {
        $all = self::where('id', '<>', $id)
            ->where('parent_id', '<>', $id)
            ->select(['id', 'name', 'sort_order', 'level'])
            ->orderBy('sort_order', 'asc')
            ->get()->toArray();
        $result = [];
        $result = array_map(function ($value) {
            if ($value['level'] == 1) {
                $value['name'] = ' |-- ' . $value['name'];
            }
            if ($value['level'] == 2) {
                $value['name'] = ' |-- |-- ' . $value['name'];
            }
            if ($value['level'] == 3) {
                $value['name'] = ' |-- |-- |-- ' . $value['name'];
            }
            if ($value['level'] == 4) {
                $value['name'] = ' |-- |-- |-- | --' . $value['name'];
            }
            return $value;
        }, $all);
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)
            ->with([
                'image',
                'banner'
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

    public static function getAllCategory(){
        $categoryService = new CategoryService();
        $categories = self::parent()->with('products')->orderBy('sort_order')->get();

        // danh mục sản phẩm
        $categories = $categories->map(function ($cate) use ($categoryService) {
            // áp dụng cho category cha
            $cate->child_categories = $categoryService->getChildCategory($cate, 1);
            return $cate;
        });

        return $categories;
    }
}
