<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\WarehouseExport as ThisModel;
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
use App\Model\Product;
use App\Model\Uptek\PrintTemplate;

class WarehouseExportController extends Controller
{
  	protected $view = 'g7.warehouse_exports';
	protected $route = 'WarehouseExport';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function searchData(Request $request)
	{
		$objects = ThisModel::searchByFilter($request);
		return Datatables::of($objects)
			->editColumn('created_at', function ($object) {
				return Carbon::parse($object->created_at)->format("H:i d/m/Y");
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name;
			})
			->editColumn('bill', function ($object) {
				if ($object->bill) return $object->bill->code;
				return '';
			})
			->editColumn('code', function ($object) {
				return $object->code;
			})
			->addColumn('action', function ($object) {
				$result = "";
				$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a>';

				return $result;
			})
			->rawColumns(['code', 'status', 'action', 'bill'])
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

	public function getDataForShow($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canView()) return view('not_found');
		$object = ThisModel::getDataForShow($id);
		return successResponse('Lấy dữ liệu thành công !',$object);
	}

  	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'type' => 'required|in:1',
			'bill_id' => 'required_if:type,1|nullable|exists:bills,id',
			'details' => 'required|array|min:1',
			'details.*.product_id' => 'required|exists:products,id',
			'details.*.qty' => 'required|numeric|min:0|max:999999999',
		];

		$validate = Validator::make(
			$request->all(),
			$rule
		);

		$json = new stdClass();

		if ($validate->fails()) return errorResponse("", $validate->errors());

		if ($request->type == ThisModel::XUAT_BAN) {
			$bill = Bill::find($request->bill_id);
			if (!$bill->canExport()) return errorResponse("Hóa đơn không hợp lệ");
		}

		$stock_valid = $this->validateStock($request->details);
		if (!$stock_valid->success) return errorResponse($stock_valid->message);

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(20);
			$object->type = $request->type;
			$object->bill_id = $request->bill_id;
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;
			$object->save();
			$object->generateCode();

			$object->syncDetails($request->details);

			$object->updateWarehouse();

			if ($object->bill) $object->updateBill();

			DB::commit();
			return successResponse();
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function validateStock($details) {
		$json = new stdClass();
		foreach ($details as $d) {
			if ($d['qty'] == 0) continue;
			$stock = Stock::where('g7_id', Auth::user()->g7_id)->where('product_id', $d['product_id'])->first();
			if (!$stock || $stock->qty < $d['qty']) {
				$json->success = false;
				$json->message = "Vật tư ".Product::find($d['product_id'])->name." không đủ số lượng";
				return $json;
			}
		}
		$json->success = true;
		return $json;
	}

	public function print($id, Request $request) {
        $object = ThisModel::where('id', $id)->firstOrFail();
		return $object->print();
    }
}
