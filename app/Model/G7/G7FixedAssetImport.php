<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use App\Model\Product;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Model\Uptek\PrintTemplate;

class G7FixedAssetImport extends BaseModel
{
	public CONST DANG_TAO = 3;
	public CONST DA_DUYET = 1;

    public CONST STATUSES = [
        [
            'id' => self::DANG_TAO,
            'name' => 'Đang tạo',
            'type' => 'primary'
        ],
        [
            'id' => self::DA_DUYET,
            'name' => 'Đã duyệt',
            'type' => 'success'
        ],
    ];

    public function canDelete() {
        return $this->status == self::DANG_TAO && auth()->user()->id == $this->created_by;
    }

	public function canEdit() {
        return $this->status == self::DANG_TAO && auth()->user()->id == $this->created_by;
    }

    public function canView()
    {
        return true;
    }

    public function canPay()
    {
        return Auth::user()->g7_id == $this->g7_id;
    }

    public function details()
    {
        return $this->hasMany(G7FixedAssetImportDetail::class, 'parent_id','id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id','id');
    }

    public function syncDetails($list) {
        G7FixedAssetImportDetail::where('parent_id', $this->id)->delete();
        if ($list) {
            foreach ($list as $l) {
				$asset = G7FixedAsset::find($l['asset_id']);
                $item = new G7FixedAssetImportDetail();
                $item->parent_id = $this->id;
                $item->name = $asset->name;
                $item->qty = $l['qty'];
                $item->price = $l['price'];
                $item->total_price = $item->price * $item->qty;
                $item->unit_name = $asset->unit_name;
                $item->unit_id = $asset->unit_id;
                $item->asset_id = $l['asset_id'];
                $item->save();
            }
        }
    }

    public static function searchByFilter($request) {
        $result = self::with(['supplier']);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'details' => function($q) {
                    $q->with([
                        'asset'
                    ]);
                }
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'details',
				'supplier',
				'user_create'
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = 'PNTSCĐ'.'.'.generateCode(5, $this->id);
        $this->save();
    }

    public static function getReportByDates($from_date, $to_date)
    {
        $rsl = self::query()
            ->selectRaw(DB::raw('sum(amount_after_vat) as total_amount'))
            ->whereBetween('import_date', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id)
            ->first();
        return $rsl->total_amount;
    }

    public function getPrintDataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrintHeader();
        $result['MA_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->import_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['NHA_CUNG_CAP'] = $this->supplier->name;
        $result['TONG_SO_LUONG'] = $this->details->count('qty');
        $result['TONG_GIA_TRI'] = formatCurrency($this->amount_after_vat);
        $result['GHI_CHU'] = $this->note;
        $result['THANH_TOAN'] = $this->pay_type == 1 ? 'Tiền mặt' : 'Chuyển khoản';

        $result['CHI_TIET_HOA_DON'] = $this->detail_table;
        $result['TONG_HOP'] = $this->bill_footer;

        return $result;
    }

    public function getDetailTableAttribute()
    {
        $data = '';
        $details = $this->details;

        foreach($details as $detail) {
            $data .= '<tr>';
            $data .= '<td style="text-align: center;">'.($detail->index + 1).'</td>';
            $data .= '<td style="text-align: center;">'.$detail->name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->unit_name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->qty.'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->total_price).'</td>';
            $data .= '</tr>';
        }

        $result = '<table style="width: 100%;">
            <thead>
                <tr>
                <td style="text-align: center"><b>STT</b></td>
                <td style="text-align: center" ><b>Tên</b></td>
                <td style="text-align: center"><b>Đơn vị</b></td>
                <td style="text-align: center"><b>Số lượng</b></td>
                <td style="text-align: center"><b>Đơn giá</b></td>
                <td style="text-align: center"><b>Thành tiền</b></td>
                </tr>
            </thead>
            <tbody>'
            .$data.
            '</tbody>
        </table>';

        return $result;
    }

    public function getBillFooterAttribute()
    {
        $result = '<table style="width: 100%; margin-top: 10px"><tbody>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">Tổng giá trị</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->amount).'</b></td>';
        $result .= '</tr>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">VAT('.$this->vat_percent.'%)</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->amount_after_vat - $this->amount).'</b></td>';
        $result .= '</tr>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">Tổng giá trị sau VAT</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->amount_after_vat).'</b></td>';
        $result .= '</tr>';
        $result .= '</tbody></table>';
        return $result;
    }

	public function print()
    {
        $template = PrintTemplate::where('id', PrintTemplate::PHIEU_NHAP_TSCD)->firstOrFail();
        $template = self::fillReport($template->template, $this->print_data);

        $template = self::clearNull($template);
        return view('print', compact('template'));
    }

}
