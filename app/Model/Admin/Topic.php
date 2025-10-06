<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Topic extends BaseModel
{
    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public function questions() {
        return $this->hasMany(Question::class,'topic_id');
    }

    public static function searchByFilter($request)
    {
        $result = self::with(['questions']);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::with('questions')->where('id', $id)->firstOrFail();
    }

    public static function getForSelect()
    {
        return self::query()
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }
}
