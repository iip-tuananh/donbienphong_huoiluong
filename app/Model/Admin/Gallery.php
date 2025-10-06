<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;

class Gallery extends BaseModel
{
    protected $table = 'gallery';

//    protected $fillable = ['post_id', 'category_special_id'];

    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public static function searchByFilter($request)
    {
        $result = self::with([
            'image',
        ]);

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::with('image')->where('id', $id)
            ->firstOrFail();
    }

    public function canDelete()
    {
        return true;
    }
}
