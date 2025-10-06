<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class PaymentVoucherType extends BaseModel
{
    public CONST NHAP_HANG = 1;
    public CONST NHAP_TSCD = 2;
    public CONST LUONG = 3;
    public CONST KHAC = 4;


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

    public function paymentVouchers()
    {
        return $this->hasMany('App\Model\G7\PaymentVoucher','payment_voucher_type_id','id');
    }

    public static function searchByFilter($request) {
        $result = self::with([]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('name','asc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();
    }
}
