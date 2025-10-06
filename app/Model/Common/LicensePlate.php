<?php

namespace App\Model\Common;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class LicensePlate extends BaseModel
{
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

    public function Car()
    {
        return $this->belongsTo('App\Model\Common\Car','license_plate_id','id');
    }


    public static function searchByFilter($request) {
        $result = self::with([]);

        if (!empty($request->license_plate)) {
            $result = $result->where('license_plate', 'like', '%'.$request->license_plate.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('license_plate','asc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'license_plate'])
            ->orderBy('license_plate', 'asc')
            ->get();
    }
}
