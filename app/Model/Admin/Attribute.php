<?php

namespace App\Model\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Attribute extends Model
{
    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return true;
    }

    public static function searchByFilter($request)
    {
        $result = self::with('products');

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        return self::with('products')->where('id', $id)
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_values', 'attribute_id', 'product_id');
    }
}
