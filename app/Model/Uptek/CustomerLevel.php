<?php

namespace App\Model\Uptek;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;

class CustomerLevel extends BaseModel
{
    protected $table = 'customer_levels';

    public function customers()
    {
        return $this->hasMany(Customer::class,'g7_id','id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'g7_id','id');
    }

    public function canEdit()
    {
        if(Auth::user()->type == 1 || Auth::user()->type == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function canDelete() {
        return true;
    }


    public static function searchByFilter($request) {
        $result = self::with([
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        $result = $result->orderBy('point','asc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with(['users'])
            ->firstOrFail();
    }

}
