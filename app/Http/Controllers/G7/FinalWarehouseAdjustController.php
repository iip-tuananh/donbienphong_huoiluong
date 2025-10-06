<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\FinalWarehouseAdjust as ThisModel;
use App\Model\G7\G7Service;
use App\Model\G7\G7Product;
use App\Model\G7\Bill;
use App\Model\G7\BillExportProduct;
use App\Model\G7\Stock;
use Yajra\DataTables\DataTables;
use Validator;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class FinalWarehouseAdjustController extends Controller
{
  	protected $view = 'g7.final_warehouse_adjusts';
	protected $route = 'FinalWarehouseAdjust';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function searchData(Request $request)
	{
		$objects = ThisModel::searchByFilter($request);
		return Datatables::of($objects)
			->editColumn('created_at', function ($object) {
				return Carbon::parse($object->created_at)->format("d/m/Y H:i");
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name;
			})
			->editColumn('code', function ($object) {
				return "<a href='".route($this->route.'.show', $object->id)."'>".$object->code."</a>";
			})
			->addColumn('action', function ($object) {
				$result = "";
				return $result;
			})
			->rawColumns(['code', 'status', 'action'])
			->addIndexColumn()
			->make(true);
	}

	public function show($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canView()) return view('not_found');
		$object = ThisModel::getDataForShow($id);
		return view($this->view.'.show', compact(['object']));
	}

  	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'bills' => 'required|array|min:1',
			'bills.*.id' => 'required|exists:bills,id',
			'bills.*.export_products' => 'required|array|min:1',
			'bills.*.export_products.*.id' => 'required|exists:bill_export_products,id',
			'bills.*.export_products.*.real_qty' => 'required|numeric|min:0'
		];

		$translate = [];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
			$json->errors = $validate->errors();
			$json->message = "Tạo thất bại!";
			return Response::json($json);
		}

		$bill_valid = $this->validateBills($request->bills);
		if (!$bill_valid->success) return Response::json($bill_valid);

		$stock_valid = $this->validateStock($request->bills);
		if (!$stock_valid->success) return Response::json($stock_valid);

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(20);
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;
			$object->save();
			$object->generateCode();

			$object->syncBills($request->bills);

			$object->updateWarehouse();

			$json->success = true;
			$json->message = 'Dữ liệu đã được cập nhật';

			DB::commit();
			return Response::json($json);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function validateBills($bills) {
		$json = new stdClass();
		foreach($bills as $b) {
			$bill = Bill::find($b['id']);
			if ($bill->status != Bill::DA_DUYET) {
				$json->success = false;
				$json->message = "Hóa đơn ".$bill->code." không hợp lệ";
				return $json;
			}
		}
		$json->success = true;
		return $json;
	}

	public function validateStock($bills) {
		$json = new stdClass();
		$object = [];
		foreach ($bills as $b) {
			foreach ($b['export_products'] as $p) {
				$export_product = BillExportProduct::where('id', $p['id'])
					->where('parent_id', $b['id'])
					->first();
				$diff = $p['real_qty'] - $export_product['qty'];
				if (!isset($object[$export_product->product_id])) $object[$export_product->product_id] = 0;
				$object[$export_product->product_id] += $diff;
			}
		}
		foreach ($object as $key => $value) {
			if ($value >= 0) continue;
			$stock = Stock::where('g7_id', Auth::user()->g7_id)->where('product_id', $key)->first();
			if (!$stock || $stock->qty < $value) {
				$json->success = false;
				$json->message = "Vật tư ".Product::find($key)->name." không đủ số lượng";
				return $json;
			}
		}
		$json->success = true;
		return $json;
	}
}
