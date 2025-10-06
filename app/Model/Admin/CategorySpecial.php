<?php

namespace App\Model\Admin;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Support\Facades\Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;

class CategorySpecial extends BaseModel
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected $table = 'category_special';

    public const PRODUCT = 10;
    public const POST = 20;
    public const TYPES = [
        [
            'id' => 10,
            'name' => 'Sản phẩm',
        ],
        [
            'id' => 20,
            'name' => 'Bài viết',
        ],
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return true;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public static function searchByFilter($request)
    {
        $result = self::with([
            'user',
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('order_number', 'asc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::select(['id', 'name', 'slug'])
            ->where(['show_home_page' => 1])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        return self::with(['image','posts'])->where('id', $id)
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_category_special', 'category_special_id', 'post_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category_special', 'category_special_id', 'product_id');
    }

}
