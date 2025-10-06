<?php

namespace App\Model\Uptek;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use DB;

class AccumulatePoint extends BaseModel
{
    protected $table = 'accumulate_points';


    public function users()
    {
        return $this->hasMany(User::class,'g7_id','id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public static function getForSelect() {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getPointRate()
    {
        return self::where('id', 1)->first()->point_to_money_rate;
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with(['users'])
            ->firstOrFail();
    }

}
