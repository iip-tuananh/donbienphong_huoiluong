<?php

namespace App\Model\Admin;

use App\Model\Common\Customer;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class ArticleSubmission extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public static function searchByFilter($request)
    {
        $result = self::query();

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%' . $request->title . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public function user_update ()
    {
        return $this->belongsTo('App\Model\Common\User', 'updated_by','id');
    }

}
