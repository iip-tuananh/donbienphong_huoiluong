<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;

class G7Employee extends BaseModel
{
    protected $table = 'g7_employees';

    public CONST HOAT_DONG = 1;
    public CONST KHOA = 0;
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

    public function user()
    {
        return $this->hasMany(User::class,'employee_id','id');
    }

    public function g7Info()
    {
        return $this->belongsTo('App\Model\Uptek\G7Info','g7_id','id');
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function canDelete() {
        if($this->user->count()) {
            return false;
        } else {
            return true;
        }
    }
    public function canEdit() {
        if($this->created_by == Auth::user()->id || Auth::user()->type == 3) {
            return true;
        } else {
            return false;
        }
    }

    public static function searchByFilter($request) {
        $result = self::with([
            'user',
            'g7Info',
            'image'
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->mobile)) {
            $result = $result->where('mobile', $request->mobile);
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
            ->select(['id', 'name','mobile'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with(['user','g7Info','image'])
            ->firstOrFail();
    }

}
