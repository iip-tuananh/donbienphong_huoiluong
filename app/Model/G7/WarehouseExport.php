<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\User;
use App\Model\Product;
use DB;
use App\Model\Uptek\PrintTemplate;

class WarehouseExport extends BaseModel
{
    protected $table = 'warehouse_exports';
    protected $fillable = ['export_value','total_qty'];

	public CONST XUAT_BAN = 1;

	public CONST TYPES = [
		[
			'id' => self::XUAT_BAN,
			'name' => 'Xuất bán'
		]
	];

    public CONST STATUSES = [

    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id','id');
    }

    public function details()
    {
        return $this->hasMany(WarehouseExportDetail::class, 'parent_id','id');
    }

    public function canView() {
        return Auth::user()->g7_id == $this->g7_id;
    }

    public static function searchByFilter($request) {
        $result = self::with([
			'bill'
		]);

        if (Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN) {

        } else if (Auth::user()->type == User::NHOM_G7) {

        } else $result = $result->where('g7_id', Auth::user()->g7_id);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

		if (!empty($request->bill)) {
			$bill_ids = Bill::where('code', 'like', '%'.$request->bill.'%')->pluck('id')->toArray();
            $result = $result->whereIn('bill_id', $bill_ids);
        }

        if (!empty($request->created_by)) {
            $result = $result->where('created_by', $request->created_by);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }

    public function syncDetails($list) {
        WarehouseExportDetail::where('parent_id', $this->id)->delete();
        if ($list) {
            $total_qty = 0;
            foreach ($list as $l) {
				$product = Product::find($l['product_id']);
				$item = new WarehouseExportDetail();
				$item->parent_id = $this->id;
				$item->product_id = $l['product_id'];
				$item->product_name = $product->name;
				$item->unit_id = $product->unit_id;
				$item->unit_name = $product->unit_name;
				$item->qty = $l['qty'];
				$item->save();
                $total_qty += $item->qty;
            }
            $this->update([
                'total_qty' => $total_qty
            ]);
        }
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'details',
                'bill'
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "PXK-".generateCode(5, $this->id);
        $this->save();
    }

    public function updateWarehouse() {
        $total_export_value = 0;
        foreach ($this->details as $p) {
            $stock = Stock::where('product_id', $p->product_id)->where('g7_id', $this->g7_id)->firstOrFail();
			$price = $stock->value / $stock->qty;
			$total = $price * $p->qty;

			$p->export_price = $price;
			$p->save();

            $log = new StockLog();
            $log->stock_id = $stock->id;
            $log->warehouse_export_detail_id = $p->id;
            $log->qty_before = $stock->qty;
            $log->change = -$p->qty;
			$log->value_before = $stock->value;

            $stock->qty -= $p->qty;
			$stock->value -= $total;
            $stock->save();

            $log->qty_after = $stock->qty;
			$log->value_after = $stock->value;
			$log->save();
            $total_export_value += $total;
        }
        $this->update([
            'export_value' => $total_export_value
        ]);
    }

	public function updateBill() {
		$this->bill->status = Bill::DA_CHOT;
		$this->bill->save();

		foreach ($this->bill->products as $p) {
			$export_product = WarehouseExportDetail::where('parent_id', $this->id)
				->where('product_id', $p->product_id)
				->first();
			if ($export_product->qty > $p->qty) $p->exported_qty = $p->qty;
			else $p->exported_qty = $export_product->qty;

			$p->export_price = $export_product->export_price;

			$p->save();
		}
	}

    public static function getReportByDates($from_date, $to_date)
    {
        $rsl = self::query()
            ->selectRaw(DB::raw('sum(export_value) as total_value'))
            ->whereBetween('created_at', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id)
            ->first();
        return $rsl->total_value;
    }

    public function getPrintDataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrintHeader();
        $result['MA_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->import_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['KHACH_HANG'] = $this->bill->customer->name;
        $result['TONG_SO_LUONG'] = $this->details->count('qty');
        $result['TONG_GIA_TRI'] = formatCurrency($this->amount_after_vat);
        $result['GHI_CHU'] = $this->note;
        $result['THANH_TOAN'] = $this->pay_type == 1 ? 'Tiền mặt' : 'Chuyển khoản';
        $result['SO_HOA_DON_BAN'] = $this->bill->code;

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
            $data .= '<td style="text-align: center;">'.$detail->product_name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->unit_name.'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->qty).'</td>';
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
        $template = PrintTemplate::where('id', PrintTemplate::PHIEU_XUAT_KHO)->firstOrFail();
        $template = self::fillReport($template->template, $this->print_data);

        $template = self::clearNull($template);
        return view('print', compact('template'));
    }

}
