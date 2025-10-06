<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class ReceiptVoucherType extends BaseModel
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

    public function receiptVouchers()
    {
        return $this->hasMany('App\Model\G7\ReceiptVoucher','receipt_voucher_type_id','id');
    }

    public static function searchByFilter($request) {
        $result = self::with([]);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

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

    public function generateCode() {
        $this->code = "LPT.".generateCode(6, $this->id);
        $this->save();
    }
}
