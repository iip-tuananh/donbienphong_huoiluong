<?php

namespace App\Model\Admin;

use Illuminate\Support\Facades\Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;

class Manufacturer extends BaseModel
{
    public function canEdit()
    {
        return Auth::user()->id = $this->create_by;
    }

    public function canDelete()
    {
        return $this->products()->count() > 0 ? false : true;
    }

    public function products() {
        return $this->hasMany(Product::class, 'manufacturer_id');
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
        ])->withCount('products');

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getForSelect()
    {
        return self::select(['id', 'name', 'code'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id)
    {
        return self::with('image')->where('id', $id)
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }

}
