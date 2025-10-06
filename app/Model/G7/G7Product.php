<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use App\Model\Product;
use App\Model\Common\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Auth;

class G7Product extends BaseModel
{

    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Khóa',
            'type' => 'danger'
        ]
    ];

    public function canDelete() {
        return true;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function root_product()
    {
        return $this->belongsTo(Product::class, 'root_product_id', 'id');
    }

    public static function searchByFilter($request) {
        $result = Product::query()
            ->with([
                'category',
                'image',
            ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->product_category_id)) {
            $result = $result->where('product_category_id', $request->product_category_id);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getData($id) {
        return self::where('id', $id)
            ->select([
                '*', 'id as g7_product_id'
            ])
            ->with([
                'category' => function($q) {
                    $q->select(['id', 'name']);
                },
                'image'
            ])
            ->firstOrFail();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'root_product',
                'image'
            ])
            ->firstOrFail();
    }
}
