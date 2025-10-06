<?php

namespace App\Model\Admin;
use App\Helpers\FileHelper;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;

class ConfigStatistic extends BaseModel
{
    protected $table = 'config_statistics';
    protected $fillable = [];


    public static function getDataForEdit($id)
    {
        return self::where('id', $id)->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

    public static function searchByFilter($request) {
        $result = self::with([
            'user',
        ]);

        if (!empty($request->title)) {
            $result = $result->where('title', 'like', '%'.$request->title.'%');
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }
}
