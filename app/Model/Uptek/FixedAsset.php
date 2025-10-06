<?php

namespace App\Model\Uptek;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use App\Model\Common\Unit;
use App\Model\Common\User;
use \stdClass;
use Auth;

class FixedAsset extends BaseModel
{
    // Status = 1 =>> Hoạt động
    // Status = 0 =>> Khóa

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

    public function canDelete() {
        return true;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public static function searchByFilter($request) {
        $result = self::with([
            'unit',
            'image'
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function canG7Use() {
        $json = new stdClass();
        $json->success = false;

        if (Auth::user()->type != User::G7 || !Auth::user()->g7_id) {
			$json->message = "Không phải tài khoản G7";
			return $json;
		}

        $json->success = true;
        return $json;
    }

    public static function getDataForG7($id) {
        return self::where('id', $id)
            ->with([
                'image'
            ])
            ->first();
    }
}
