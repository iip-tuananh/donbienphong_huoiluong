<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Question extends BaseModel
{
    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }


    public function topic()
    {
        return $this->belongsTo(Topic::class,'topic_id');
    }

    public static function searchByFilter($request)
    {
        $result = self::with(['topic']);

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        if (!empty($request->topic_id)) {
            $result = $result->where('topic_id', $request->topic_id );
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::with('topic')->where('id', $id)->firstOrFail();
    }
}
