<?php

namespace App\Model\Uptek;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Vanthao03596\HCVN\Models\Province;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Ward;
use DB;

class G7Info extends BaseModel
{
    protected $table = 'g7_infos';
    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Khóa',
            'type' => 'danger'
        ]
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class,'g7_id','id');
    }

    public function users()
    {
        return $this->hasMany(User::class,'g7_id','id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function canDelete() {
        return Auth::user()->id == $this->create_by && $this->users->count() == 0;
    }

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public function district()
    {
        return $this->belongsTo(District::class,'district_id','id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class,'ward_id','id');
    }


    public function getFullAdressAttribute($value)
    {
        if($this->adress) {
            return "{$this->adress}, {$this->ward->name}, {$this->district->name}, {$this->province->name}";
        } else
        {
            return "{$this->ward->name}, {$this->district->name}, {$this->province->name}";
        }
    }

    public static function searchByFilter($request) {
        $result = self::with([
            'users',
            'image'
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->mobile)) {
            $result = $result->where('mobile', $request->mobile);
        }

        if (!empty($request->province_id)) {
            $result = $result->where('province_id', $request->province_id);
        }

        if (!empty($request->district_id)) {
            $result = $result->where('district_id', $request->district_id);
        }

        if (!empty($request->ward_id)) {
            $result = $result->where('ward_id', $request->ward_id);
        }

        if (!empty($request->user_id)) {
            $result = $result->where('user_id', $request->user_id);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
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
