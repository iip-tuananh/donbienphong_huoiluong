<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use App\Model\Uptek\PrintTemplate;
use Illuminate\Support\Facades\Auth;
use DB;

class ReceiptVoucher extends BaseModel
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
    public CONST PAYER_TYPES = [
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

    public function getPayerType($type_id)
    {
        foreach(self::PAYER_TYPES as $type) {
            if($type['id'] == $type_id) {
                return $type['name'];
            }
        }
    }

    public function getPrintDataAttribute($value)
    {
        $result = [];
        $result['invoice_header'] = getPrintHeader();
        $result['TITLE'] = 'PHIẾU THU TIỀN';
        $result['SO_PHIEU'] = $this->code;
        $result['LOAI_PHIEU'] = $this->receiptVoucherType->name;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->record_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['DOI_TUONG_TRA'] = $this->getPayerType($this->pay_type);
        $result['NGUOI_TRA'] = $this->payer_name ? $this->payer_name : $this->payer->name;
        $result['TONG_GIA_TRI'] = formatCurrent($this->value);
        if($this->bill_id) {
            $result['SO_HOA_DON'] = $this->bill->code;
        }
        $result['GHI_CHU'] = $this->note;

        return $result;
    }

    public function getPrintTemplateAttribute() {

        $template = PrintTemplate::where('id', PrintTemplate::PHIEU_THU_TIEN)->firstOrFail();
        $template = self::fillReport($template->template, $this->print_data);
        $template = self::clearNull($template);
        return $template;
    }

    public function canDelete() {
        return true;
    }

    public function payer()
    {
        return $this->morphTo();
    }

    public function receiptVoucherType()
    {
        return $this->belongsTo('App\Model\G7\ReceiptVoucherType','receipt_voucher_type_id','id');
    }

    public function bill()
    {
        return $this->belongsTo('App\Model\G7\Bill','bill_id','id');
    }


    public static function searchByFilter($request) {
        $result = self::with([]);

    if (!empty($request->code)) {
        $result = $result->where('code', 'like', '%'.$request->code.'%');
    }

    if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
        $result = $result->where('status', $request->status);
    }

    $result = $result->orderBy('code','asc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'code'])
        ->orderBy('code', 'asc')
        ->get();
    }

    public function generateCode() {
        $this->code = "PTT.".generateCode(6, $this->id);
        $this->save();
    }

    public static function getReportByDates($from_date, $to_date, $type_except = [])
    {
        $query = self::query()
            ->selectRaw(DB::raw('receipt_voucher_type_id, sum(value) as total_value'))
            ->whereBetween('record_date', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id);
        if (!empty($type_except)) {
            $query->whereNotIn('receipt_voucher_type_id', $type_except);
        }
        $results = $query->groupBy('receipt_voucher_type_id')
            ->orderBy('receipt_voucher_type_id')
            ->pluck('total_value', 'receipt_voucher_type_id')
            ->toArray();

        if (!empty($type_except)) {
            $types = ReceiptVoucherType::query()
                ->whereNotIn('id', $type_except)
                ->orderBy('id')->pluck('name', 'id')->toArray();
        } else {
            $types = ReceiptVoucherType::query()->orderBy('id')->pluck('name', 'id')->toArray();
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

        $total_value = array_sum(array_map(function($item) {
            return $item['total_value'];
        }, $report));

        return [$total_value, $report];
    }
}
