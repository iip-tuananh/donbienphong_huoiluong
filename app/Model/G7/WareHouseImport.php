<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use App\Model\Product;
use Illuminate\Database\Eloquent\Model;
use App\Model\Uptek\PrintTemplate;

class WareHouseImport extends BaseModel
{
	public CONST DANG_TAO = 3;
	public CONST DA_DUYET = 1;

	public CONST NHAP_KHAC = 1;
	public CONST NHAP_TRA = 2;

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

    protected $appends = ['created_format'];

    public function canDelete() {
        return $this->status == self::DANG_TAO && auth()->user()->id == $this->created_by && $this->paymentVoucher->count() == 0;
    }

	public function canEdit() {
        return $this->status == self::DANG_TAO && auth()->user()->id == $this->created_by;
    }

	public function canPay() {
        return auth()->user()->g7_id == $this->g7_id;
    }

    public function paymentVoucher()
    {
        return $this->hasMany('App\Model\G7\PaymentVoucher','ware_house_import_id','id');
    }

    public function products()
    {
        return $this->hasMany(WareHouseImportDetail::class,'ware_house_import_id','id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id','id');
    }

    public function syncProducts($importProducts) {
        WareHouseImportDetail::where('ware_house_import_id', $this->id)->delete();
        if ($importProducts) {
            foreach ($importProducts as $product) {
                $item = new WareHouseImportDetail();
                $item->name = $product['name'];
                $item->qty = $product['qty'];
                $item->ware_house_import_id = $this->id;
                $item->import_price = $product['import_price'];
                $item->amount = $product['amount'];
                $item->unit_name = $product['unit_name'];
                $item->unit_id = $product['unit_id'];
                $item->product_id = $product['product_id'];
                $item->save();
            }
        }
    }

    public static function searchByFilter($request) {
        $result = self::with(['supplier']);

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
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'products' => function($q) {
                    $q->with([
                        'product' => function($q) {
                            $q->select(['id', 'name','unit_name','unit_id']);
                        }
                    ]);
                }
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'products' => function($q) {
                    $q->with([
                        'product' => function($q) {
                            $q->select(['*']);
                        }
                    ]);
                }
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = 'PNH'.'.'.generateCode(5, $this->id);
        $this->save();
    }

    public function updateWarehouse() {
        foreach ($this->products as $p) {
            $stock = Stock::where('product_id', $p->product_id)->where('g7_id', $this->g7_id)->first();
            if (!$stock) {
                $stock = new Stock();
                $stock->g7_id = $this->g7_id;
                $stock->product_id = $p->product_id;
                $stock->qty = 0;
				$stock->value = 0;
                $stock->save();
            }

			$value = $p->qty * $p->import_price;

            $log = new StockLog();
            $log->stock_id = $stock->id;
            $log->warehouse_import_detail_id = $p->id;
            $log->qty_before = $stock->qty;
            $log->change = $p->qty;
			$log->value_before = $stock->value;

            $stock->qty += $p->qty;
			$stock->value += $value;
            $stock->save();

            $log->qty_after = $stock->qty;
            $log->value_after = $stock->value;
            $log->save();
        }
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
        $result['TONG_SO_LUONG'] = $this->products->count('qty');
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
        $products = $this->products;

        foreach($products as $detail) {
            $data .= '<tr>';
            $data .= '<td style="text-align: center;">'.($detail->index + 1).'</td>';
            $data .= '<td style="text-align: center;">'.$detail->name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->unit_name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->qty.'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->import_price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->amount).'</td>';
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
        $template = PrintTemplate::where('id', PrintTemplate::PHIEU_NHAP_KHO)->firstOrFail();
        $template = self::fillReport($template->template, $this->print_data);

        $template = self::clearNull($template);
        return view('print', compact('template'));
    }
}
