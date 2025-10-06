<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Illuminate\Support\Facades\Auth;
use DB;

class PaymentVoucher extends BaseModel
{
    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Đã duyệt',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Hủy',
            'type' => 'danger'
        ]
    ];
// Đối tượng nhận phí
    public CONST RECIPIENT_TYPES = [
        [
            'id' => 1,
            'name' => 'Khách hàng',
        ],
        [
            'id' => 2,
            'name' => 'Nhân viên',
        ],
        [
            'id' => 3,
            'name' => 'Nhà cung cấp',
        ],
        [
            'id' => 4,
            'name' => 'Khác',
        ]
    ];

    public function getRecipientType($type_id)
    {
        foreach(self::RECIPIENT_TYPES as $type) {
            if($type['id'] == $type_id) {
                return $type['name'];
            }
        }
    }

    protected $appends = ['created_format'];


    public function canDelete() {
        return true;
    }

    public function recipientale()
    {
        return $this->morphTo();
    }

    public function paymentVoucherType()
    {
        return $this->belongsTo('App\Model\G7\PaymentVoucherType','payment_voucher_type_id','id');
    }

    public function wareHouseImport()
    {
        return $this->belongsTo('App\Model\G7\WareHouseImport','ware_house_import_id','id');
    }

    public function g7FixedAssetImport()
    {
        return $this->belongsTo('App\Model\G7\G7FixedAssetImport','g7_fixed_asset_import_id','id');
    }


    public static function searchByFilter($request) {
        $result = self::with([]);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'code'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function generateCode() {
        $this->code = "PCT-".generateCode(5, $this->id);
        $this->save();
    }

    public static function getReportByDates($from_date, $to_date, $type_except = [])
    {
        $query = self::query()
            ->selectRaw(DB::raw('payment_voucher_type_id, sum(value) as total_value'))
            ->whereBetween('record_date', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id);
        if (!empty($type_except)) {
            $query->whereNotIn('payment_voucher_type_id', $type_except);
        }
        $results = $query->groupBy('payment_voucher_type_id')
            ->orderBy('payment_voucher_type_id')
            ->pluck('total_value', 'payment_voucher_type_id')
            ->toArray();

        if (!empty($type_except)) {
            $types = PaymentVoucherType::query()
                ->whereNotIn('id', $type_except)
                ->orderBy('id')->pluck('name', 'id')->toArray();
        } else {
            $types = PaymentVoucherType::query()->orderBy('id')->pluck('name', 'id')->toArray();
        }

        $report = [];
        foreach ($types as $id => $name) {
            if (!isset($results[$id])) {
                $report[] = [
                    'name' => $name,
                    'total_value' => 0
                ];
            } else {
                $report[] = [
                    'name' => $name,
                    'total_value' => $results[$id]
                ];
            }
        }

        $total_point_money = Bill::query()
            ->whereBetween('bill_date', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id)
            ->where('status', Bill::DA_DUYET)
            ->sum('point_money');

        $report[] = [
            'name' => 'Chi tiêu điểm',
            'total_value' => $total_point_money
        ];

        $total_value = array_sum(array_map(function($item) {
            return $item['total_value'];
        }, $report));

        return [$total_value, $report];
    }
}
