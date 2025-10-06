<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\BillReturn as ThisModel;
use App\Model\G7\Bill;
use App\Model\G7\BillProduct;
use App\Model\Product;
use App\Model\G7\Stock;
use Yajra\DataTables\DataTables;
use Validator;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use App\Model\Uptek\AccumulatePoint;
use App\Model\Common\PromoCampaign;
use \Carbon\Carbon;
use DB;

class BillReturnController extends Controller
{
  	protected $view = 'g7.bill_returns';
	protected $route = 'BillReturn';

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
			->editColumn('bill_date', function ($object) {
				return $object->bill_date ? Carbon::parse($object->bill_date)->format("d/m/Y H:i") : '';
			})
			->editColumn('bill', function ($object) {
				return $object->bill->code;
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name;
			})
			->editColumn('cost_after_vat', function ($object) {
				return formatCurrency($object->cost_after_vat);
			})
			->editColumn('status', function ($object) {
				return getStatus($object->status, ThisModel::STATUSES);
			})
			->addColumn('action', function ($object) {
				$result = "";
				if ($object->canView()) {
					$result = '<a href="' . route($this->route.'.show', $object->id) . '" title="Xem chi tiết" class="btn btn-sm btn-info"><i class="far fa-eye"></i></a> ';
				}
				if ($object->canEdit()) {
					$result .= '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Hủy" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->rawColumns(['code', 'status', 'action'])
			->addIndexColumn()
			->make(true);
	}

	public function edit($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canEdit()) return view('not_found');
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

	public function show($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canView()) return view('not_found');
		$object = ThisModel::getDataForShow($id);
		return view($this->view.'.show', compact(['object']));
	}

	public function getDataForShow($id)
	{
		$object = ThisModel::findOrFail($id);
		if ($object->canView()) {
			return successResponse("", ThisModel::getDataForShow($id));
		} else {
			return errorResponse("Lấy dữ liệu thất bại");
		}
	}

  	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'bill_date' => 'required|date',
			'bill_id' => 'required|exists:bills,id',
			'status' => 'required|in:1,3',

			'products' => 'required|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',

			'total_cost' => 'required|numeric|min:1|max:999999999999',
		];

		$translate = [];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		$json = new stdClass();

		if ($validate->fails()) return errorResponse("", $validate->errors());

		$product_valid = $this->validateProducts($request->bill_id, $request->products);
		if (!$product_valid->success) return errorResponse($product_valid->message);

		$bill = Bill::find($request->bill_id);
		if (!$bill->canReturn()) return errorResponse("Hóa đơn không hợp lệ");

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(20);
			$object->bill_date = $request->bill_date;

			$object->bill_id = $request->bill_id;
			$object->license_plate = $bill->license_plate;
			$object->customer_name = $bill->customer_name;

			$object->total_cost = $request->total_cost;
			$object->vat_percent = $bill->vat_percent;
			$object->vat_cost = round($object->total_cost * $bill->vat_percent / 100);
			$object->cost_after_vat = $object->total_cost + $object->vat_cost;

			$object->status = $request->status;
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approved_time = Carbon::now();
			}

			$object->save();
			$object->generateCode();

			$object->syncProducts($request->products);

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approve();
			}

			DB::commit();
			return successResponse();
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}


	public function update(Request $request, $id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canEdit()) return errorResponse("Hóa đơn không hợp lệ");

		$rule = [
			'bill_date' => 'required|date',
			'status' => 'required|in:1,3',

			'products' => 'required|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',

			'total_cost' => 'required|numeric|min:1|max:999999999999',
		];

		$translate = [];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		if ($validate->fails()) return errorResponse("", $validate->errors());

		$product_valid = $this->validateProducts($object->bill_id, $request->products);
		if (!$product_valid->success) return errorResponse($product_valid->message);

		DB::beginTransaction();
		try {
			$object->bill_date = $request->bill_date;

			$object->total_cost = $request->total_cost;
			$object->vat_cost = round($object->total_cost * $bill->vat_percent / 100);
			$object->cost_after_vat = $object->total_cost + $object->vat_cost;

			$object->status = $request->status;
			$object->note = $request->note;

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approved_time = Carbon::now();
			}

			$object->save();

			$object->syncProducts($request->products);

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approve();
			}

			DB::commit();
			return successResponse();
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function delete($id) {
		$object = ThisModel::findOrFail($id);
		if ($object->canDelete()) {
			$object->removeFromDB();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		} else {
			$message = array(
				"message" => "Không thể hủy!",
				"alert-type" => "warning"
			);
		}
    	return redirect()->route($this->route.'.index')->with($message);
	}

	public function validateProducts($bill_id, $products) {
		$json = new stdClass();
		foreach ($products as $p) {
			$bill_product = BillProduct::where('parent_id', $bill_id)
				->where('product_id', $p['product_id'])
				->first();
			if ($bill_product->exported_qty - $bill_product->returned_qty < $p['qty']) {
				$json->success = false;
				$json->message = "Hàng hóa ".$bill_product->name." có số lượng không hợp lệ";
				return $json;
			}
		}

		$json->success = true;
		return $json;
	}

}
