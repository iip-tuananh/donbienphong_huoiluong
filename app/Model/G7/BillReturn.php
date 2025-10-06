<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\Car;
use App\Model\Common\User;
use App\Model\Common\ActivityLog;
use App\Model\Uptek\Service;
use App\Model\Uptek\ServiceVehicleCategoryGroup;
use App\Model\Product;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Model\Uptek\PrintTemplate;
use App\Model\Common\PromoCampaign;

class BillReturn extends BaseModel
{
    protected $table = 'bill_returns';

    public CONST DA_DUYET = 1;
    public CONST DANG_TAO = 3;

    public CONST STATUSES = [
        [
            'id' => self::DA_DUYET,
            'name' => 'Đã duyệt',
            'type' => 'success'
        ],
        [
            'id' => self::DANG_TAO,
            'name' => 'Đang tạo',
            'type' => 'danger'
        ]
    ];

    public function getPrintDataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrintHeader();
        $result['SO_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->bill_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['BIEN_SO_XE'] = $this->license_plate;
        $result['KHACH_HANG'] = $this->customer_name;
        $result['TONG_GIA_TRI'] = formatCurrency($this->cost_after_vat);
        $result['GHI_CHU'] = $this->note;

        $result['CHI_TIET_HOA_DON'] = $this->detail_table;
        $result['TONG_HOP'] = $this->bill_footer;

        return $result;
    }

    public function getPrint57DataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrint57Header();
        $result['SO_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->bill_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['BIEN_SO_XE'] = $this->license_plate;
        $result['KHACH_HANG'] = $this->customer_name;
        $result['DIEN_THOAI_KHACH'] = $this->bill->customer->mobile;
        $result['TONG_GIA_TRI'] = formatCurrency($this->cost_after_vat);
        $result['GHI_CHU'] = $this->note;

        $result['CHI_TIET_HOA_DON'] = $this->detail_57;
        $result['TONG_HOP'] = $this->bill_footer;

        return $result;
    }


    public function getDetailTableAttribute()
    {
        $data = '';
        $bill_detail = $this->products;

        foreach($bill_detail as $index => $detail) {
            $data .= '<tr>';
            $data .= '<td style="text-align: center;">'.($index + 1).'</td>';
			$data .= '<td style="text-align: center;">'.$detail->product_name.'</td>';
			$data .= '<td style="text-align: center;">'.$detail->unit_name.'</td>';
            $data .= '<td style="text-align: center;">'.$detail->qty.'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price * $detail->qty).'</td>';
            $data .= '</tr>';
        }

        $result = '<table style="width: 100%;">
            <thead>
                <tr>
                <td style="text-align: center"><b>STT</b></td>
                <td style="text-align: center"><b>Tên</b></td>
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

    public function getDetail57Attribute()
    {
        $data = '';
        $bill_detail = $this->products;

        foreach($bill_detail as $index => $detail) {
			$data .= '<tr><td colspan="3" style="border:none">'. ($index + 1).'-'.$detail->product_name.'</td></tr>';
            $data .= '<tr>';
			$data .= '<td style="text-align: center">';
			$data .= '<p>'.formatCurrency($detail->qty).'.<b>'.$detail->unit_name.'</b></p>';
			$data .= '</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price * $detail->qty).'</td>';
            $data .= '</tr>';
            $data .= '<tr><td colspan="3"><div style="width:100%;border-top: 1px dashed #ccc;"></div></td></tr>';
        }

        $result = '<style>table tr td {border: none !important;padding: 3px !important;font-size: 10px;}</style><table style="width: 100%; border:none">
            <thead>
                <tr>
                    <td style="text-align: center" ><b>Số lượng/Đơn vị</b></td>
                    <td style="text-align: center"><b>Đơn giá</b></td>
                    <td style="text-align: center"><b>Thành tiền</b></td>
                </tr>
                <tr><td colspan="3"><div style="width:100%;border-top: 1px solid #ccc;"></div></td></tr>
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
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->total_cost).'</b></td>';
        $result .= '</tr>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">VAT('.$this->vat_percent.'%)</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->vat_cost).'</b></td>';
        $result .= '</tr>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">Tổng giá trị sau VAT</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->cost_after_vat).'</b></td>';
        $result .= '</tr>';
        $result .= '</tbody></table>';
        return $result;
    }

	public function print($type) {
		if ($type == '57') {
            $template = PrintTemplate::where('id', PrintTemplate::HOA_DON_TRA_57)->firstOrFail();
            $template = self::fillReport($template->template, $this->print_57_data);
        } else {
            $template = PrintTemplate::where('id', PrintTemplate::HOA_DON_TRA)->firstOrFail();
            $template = self::fillReport($template->template, $this->print_data);
        }

        $template = self::clearNull($template);
        if ($type == '57') return view('print57', compact('template'));
		else return view('print', compact('template'));
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(BillReturnProduct::class, 'parent_id','id');
    }

    public function canDelete() {
        return $this->status == self::DANG_TAO && Auth::user()->id == $this->created_by;
    }

    public function canEdit() {
        return $this->status == self::DANG_TAO && Auth::user()->id == $this->created_by;
    }

    public function canView() {
        return Auth::user()->id == $this->created_by || Auth::user()->is_super_admin;
    }

    public static function searchByFilter($request) {
        $result = self::query();

        if (Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN) {

        } else if (Auth::user()->type == User::NHOM_G7) {

        } else $result = $result->where('g7_id', Auth::user()->g7_id);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

		if (!empty($request->bill)) {
            $ids = Bill::where('code', 'like', '%'.$request->code.'%')->pluck('id')->toArray();
			$result = $result->whereIn('bill_id', $ids);
        }

        if (!empty($request->license_plate)) {
            $result = $result->where('license_plate', 'like', '%'.$request->license_plate.'%');
        }

        if (!empty($request->customer_name)) {
            $result = $result->where('customer_name', 'like', '%'.$request->customer_name.'%');
        }

        if (!empty($request->created_by)) {
            $result = $result->where('created_by', $request->created_by);
        }

        if (!empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }

    public function syncProducts($list) {
        BillReturnProduct::where('parent_id', $this->id)->delete();
        if ($list) {
            foreach ($list as $index => $l) {
				$product = BillProduct::where('parent_id', $this->bill_id)->where('product_id', $l['product_id'])->first();
				$item = new BillReturnProduct();
				$item->parent_id = $this->id;
				$item->bill_product_id = $product->id;
				$item->product_id = $l['product_id'];
				$item->product_name = $product->name;
				$item->unit_id = $product->unit_id;
				$item->unit_name = $product->unit_name;
				$item->qty = $l['qty'];
				$item->price = $product->price;
				$item->import_price = $product->export_price;
				$item->save();
            }
        }
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'products' => function($q) {
                    $q->with([
                        'bill_product'
                    ]);
                },
                'bill',
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'products',
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "HĐT-".generateCode(5, $this->id);
        $this->save();
    }

    public function approve() {
		ActivityLog::createRecord("Tạo hóa đơn trả", route('BillReturn.show', $this->id, false));
		$qty = 0;
		$amount = 0;
		foreach ($this->products as $p) {
			$qty += $p->qty;
			$amount += $p->qty * $p->import_price;
        }

		$import = new WareHouseImport();
		$import->type = WareHouseImport::NHAP_TRA;
		$import->bill_return_id = $this->id;
		$import->code = randomString(8);
		$import->import_date = $this->bill_date;
		$import->qty = $qty;
		$import->pay_type = 1;
		$import->vat_percent = 0;
		$import->amount = $amount;
		$import->amount_after_vat = $amount;
		$import->note = '';
		$import->g7_id = Auth::user()->g7_id;
		$import->status = WareHouseImport::DA_DUYET;
		$import->save();

		$import->generateCode();

        foreach ($this->products as $p) {
			$item = new WareHouseImportDetail();
			$item->name = $p->product_name;
			$item->qty = $p->qty;
			$item->ware_house_import_id = $import->id;
			$item->import_price = $p->import_price;
			$item->amount = $item->qty * $item->import_price;
			$item->unit_name = $p->unit_name;
			$item->unit_id = $p->unit_id;
			$item->product_id = $p->product_id;
			$item->save();

			$p->bill_product->returned_qty += $p->qty;
			$p->bill_product->save();
        }

		$import->updateWarehouse();
    }

	public function removeFromDB() {
		BillReturnProduct::where('parent_id', $this->id)->delete();
		$this->delete();
	}

}
