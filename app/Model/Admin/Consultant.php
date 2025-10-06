<?php

namespace App\Model\Admin;
use Illuminate\Support\Facades\Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;

class Consultant extends BaseModel
{
    public function image()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image');
    }

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)->with(['image'])
            ->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->firstOrFail();
    }

    public static function searchByFilter($request) {
        $result = self::query()->with(['image']);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->phone)) {
            $result = $result->where('phone', 'like', '%'.$request->phone.'%');
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }
}
