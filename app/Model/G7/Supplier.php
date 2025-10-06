<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Model\Common\User;

class Supplier extends BaseModel
{
    public CONST HOAT_DONG = 1;
    public CONST DA_KHOA = 0;

    public CONST STATUSES = [
        [
            'id' => self::HOAT_DONG,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => self::DA_KHOA,
            'name' => 'Đã khóa',
            'type' => 'danger'
        ]
        ];

    public function canDelete() {
        return true;
    }

    public function canEdit() {
        return $this->created_by == Auth::user()->id;
    }

    public static function searchByFilter($request) {
        $result = self::with([]);

        if(Auth::user()->type == 3) {
            $result = $result->where('g7_id', Auth::user()->g7_id);
        }

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->mobile)) {
            $result = $result->where('mobile', 'like', '%'.$request->mobile.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('name','asc');

        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->where('status',1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id',$id)->first();
    }

    public function generateCode($stt) {
        $this->code = "NCC.".User::find(Auth::user()->id)->g7Info->id.'.'.generateCode(4, $stt);
        $this->save();
    }
}
