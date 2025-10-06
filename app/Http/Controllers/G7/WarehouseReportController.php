<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\G7\FinalWarehouseAdjustDetail;
use App\Model\G7\BillExportProduct;
use App\Model\G7\Bill;
use App\Model\G7\WareHouseImportDetail;
use App\Model\G7\ReceiptVoucher;
use App\Model\G7\PaymentVoucher;
use Auth;
use Validator;
use DB;
use \stdClass;
use Response;
use \Carbon\Carbon;

class WarehouseReportController extends Controller
{
	protected $view = 'g7.warehouse_reports';

    public function stockReport(Request $request) {
        return view($this->view.'.stockReport', compact([]));
    }

    public function stockReportSearchData(Request $request) {
        $json = new stdClass();
        $json->success = true;
        $json->draw = $request->draw;

        $json->data = $this->stockReportQuery($request)->paginate($request->per_page, '', 'page', $request->current_page);
		$json->total = $this->stockReportQuery($request, true)->first();
        return Response::json($json);
    }

	public function stockReportQuery($request, $total = false) {
		$export = BillExportProduct::from('bill_export_products as bep')
			->join('bills as b', 'bep.parent_id', '=', 'b.id')
			->where('b.g7_id', auth()->user()->g7_id)
			->select([
				'bep.id', 'bep.qty', 'bep.product_id', DB::raw('1 as type')
			]);

		$adjust = FinalWarehouseAdjustDetail::from('final_warehouse_adjust_details as fwad')
			->join('final_warehouse_adjusts as fwa', 'fwad.parent_id', '=', 'fwad.id')
			->where('fwa.g7_id', auth()->user()->id)
			->select([
				'fwad.id', 'fwad.change as qty', 'fwad.product_id', DB::raw('2 as type')
			]);

		$import = WareHouseImportDetail::from('ware_house_import_detail as wid')
			->join('ware_house_imports as wi', 'wid.ware_house_import_id', '=', 'wi.id')
			->where('wi.g7_id', auth()->user()->id)
			->select([
				'wid.id', 'wid.qty', 'wid.product_id', DB::raw('3 as type')
			]);

		$selectSub = [
			DB::raw('SUM(CASE WHEN type = 1 OR type = 2 THEN qty ELSE 0 END) as export_qty'),
			DB::raw('SUM(CASE WHEN type = 3 THEN qty ELSE 0 END) as import_qty'),
			DB::raw('SUM(CASE WHEN type = 1 OR type = 2 THEN -qty ELSE qty END) as stock_qty'),
			'product_id'
		];

		$union = DB::table( DB::raw("({$export->union($adjust)->union($import)->toRawSql()}) as sub") )
			->select($selectSub)->groupBy('product_id');

		if ($total) {
			$select = [
				DB::raw("SUM(export_qty) as export_qty"),
				DB::raw("SUM(import_qty) as import_qty"),
				DB::raw("SUM(stock_qty) as stock_qty"),
				DB::raw("SUM(stock_qty * p.price) as stock_value"),
			];
		} else {
			$select = [
				DB::raw("SUM(export_qty) as export_qty"),
				DB::raw("SUM(import_qty) as import_qty"),
				DB::raw("SUM(stock_qty) as stock_qty"),
				DB::raw("SUM(stock_qty * p.price) as stock_value"),
				'product_id',
				'p.name as product_name',
				'p.code as product_code',
				'p.unit_name as unit_name'
			];
		}
		$result = DB::table( DB::raw("({$union->toRawSql()}) as sub1"))
			->join('products as p', 'sub1.product_id', '=', 'p.id')
			->select($select);

		if (!$total) $result = $result->groupBy(['product_id', 'p.name', 'p.code', 'p.unit_name']);
		return $result;
	}

	public function saleReport(Request $request) {
        return view($this->view.'.saleReport', compact([]));
    }

    public function saleReportSearchData(Request $request) {
        $json = new stdClass();
        $json->success = true;
        $json->draw = $request->draw;

        $json->data = $this->saleReportQuery($request)->paginate($request->per_page, '', 'page', $request->current_page);
		$json->total = $this->saleReportQuery($request, true)->first();
        return Response::json($json);
    }

	public function saleReportQuery($request, $total = false) {

		if ($total) {
			$select = [
				DB::raw("SUM(total_cost) as total_cost"),
				DB::raw("SUM(sale_cost) as sale_cost"),
				DB::raw("SUM(cost_after_sale) as cost_after_sale"),
			];
		} else {
			$select = [
				DB::raw("SUM(total_cost) as total_cost"),
				DB::raw("SUM(sale_cost) as sale_cost"),
				DB::raw("SUM(cost_after_sale) as cost_after_sale"),
				DB::raw("DATE(bill_date) as day"),
			];
		}

		$result = Bill::where('g7_id', auth()->user()->g7_id)
			->select($select);

		if (!empty($request->from_date)) {
			$result = $result->where('bill_date', '>=', $request->from_date);
		}

		if (!empty($request->to_date)) {
			$result = $result->where('bill_date', '<', addDay($request->to_date));
		}

		if (!$total) $result = $result->groupBy([DB::raw('DATE(bill_date)')]);
		return $result;
	}

	public function fundReport(Request $request) {
        return view($this->view.'.fundReport', compact([]));
    }

    public function fundReportSearchData(Request $request) {
        $json = new stdClass();
        $json->success = true;
        $json->draw = $request->draw;

        $json->data = $this->fundReportQuery($request)->paginate($request->per_page, '', 'page', $request->current_page);
		$json->total = $this->fundReportQuery($request, true)->first();
        return Response::json($json);
    }

	public function fundReportQuery($request, $total = false) {

		$receipt = ReceiptVoucher::from('receipt_vouchers as rv')
			->leftJoin('bills as b', 'rv.bill_id', '=', 'b.id')
			->leftJoin('receipt_voucher_types as rvt', 'rv.receipt_voucher_type_id', '=', 'rvt.id')
			->select([
				'rv.code', 'rv.id as record_id', DB::raw('1 as type'), 'rv.record_date', 'rv.created_at', 'rvt.name as type_name',
				'rv.value', 'rv.note', 'rv.payer_name as object_name', 'b.id as ref_id', 'b.code as ref_code', 'rv.pay_type'
			]);

		$payment = PaymentVoucher::from('payment_vouchers as pv')
			->leftJoin('ware_house_imports as wi', 'pv.ware_house_import_id', '=', 'wi.id')
			->leftJoin('payment_voucher_types as pvt', 'pv.payment_voucher_type_id', '=', 'pvt.id')
			->select([
				'pv.code', 'pv.id as record_id', DB::raw('2 as type'), 'pv.record_date', 'pv.created_at', 'pvt.name as type_name',
				'pv.value', 'pv.note', 'pv.recipient_name as object_name', 'wi.id as ref_id', 'wi.code as ref_code', 'pv.pay_type'
			]);

		$result = DB::table( DB::raw("({$receipt->union($payment)->toRawSql()}) as sub") );

		if (!empty($request->from_date)) {
			$result = $result->where('sub.record_date', '>=', $request->from_date);
		}

		if (!empty($request->to_date)) {
			$result = $result->where('sub.record_date', '<', addDay($request->to_date));
		}

		if (!empty($request->payment_method)) {
			$result = $result->where('sub.pay_type', $request->payment_method);
		}

		if (!empty($request->object_name)) {
			$result = $result->where('sub.object_name', 'likes', '%'.$request->object_name.'%');
		}

		if ($total) {
			$result = $result->select([
				DB::raw('SUM(CASE WHEN type = 1 THEN value ELSE 0 END) as income'),
				DB::raw('SUM(CASE WHEN type = 2 THEN value ELSE 0 END) as spending')
			]);
		} else {
			$result = $result->select(['*'])->orderBy('sub.record_date', 'DESC');
		}

		return $result;
	}
}
