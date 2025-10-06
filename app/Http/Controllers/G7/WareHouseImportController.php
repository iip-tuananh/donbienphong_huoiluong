<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\WareHouseImport as ThisModel;
use Spatie\Permission\Models\Permission;
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

class WareHouseImportController extends Controller
{
  	protected $view = 'g7.warehouse_imports';
	protected $route = 'WareHouseImport';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataBySupplier(Request $request, $id)
	{
		$json = new stdClass();
		$json->success = true;
		$json->data = ThisModel::where('supplier_id',$id)->orderBy('import_date','desc')->get();
		$json->message = "Lấy dữ liệu thành công";
		return Response::json($json);
	}

	public function getDataForPay($id)
	{
		$invoice = ThisModel::find($id);

		$json = new stdClass();
		if($invoice) {
			$json->success = true;
			$json->data = $invoice;
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Lấy dữ liệu thất bại";
			return Response::json($json);
		}
	}

	public function getDataForShow($id)
	{
		$invoice = ThisModel::where('id', $id)
		->with([
			'products' => function($q) {
				$q->with([
					'product' => function($q) {
						$q->select(['*']);
					}
				]);
			},
			'supplier',
			'user_create'
		])
		->firstOrFail();

		$json = new stdClass();
		if($invoice) {
			$json->success = true;
			$json->data = $invoice;
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Lấy dữ liệu thất bại";
			return Response::json($json);
		}

	}

	// Hàm lấy data cho bảng list
	public function searchData(Request $request)
	{
		$objects = ThisModel::searchByFilter($request);
		return Datatables::of($objects)
			->editColumn('created_at', function ($object) {
				return Carbon::parse($object->created_at)->format("d/m/Y");
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name;
			})
			->editColumn('code', function ($object) {
				return $object->code;
			})
			->editColumn('pay_status', function ($object) {
				if($object->payed_value < $object->amount_after_vat) {
					return "<span class='badge badge-warning'>Còn công nợ</span>";
				} else
				{
					return "<span class='badge badge-success'>Đã thanh toán</span>";
				}
			})
			->editColumn('supplier', function ($object) {
				return $object->supplier ? $object->supplier->name : "";
			})
			->editColumn('amount', function ($object) {
				return formatCurrent($object->amount);
			})
			->editColumn('vat', function ($object) {
				return formatCurrent($object->amount * $object->vat_percent / 100);
			})
			->editColumn('amount_after_vat', function ($object) {
				return formatCurrent($object->amount_after_vat);
			})
			->addColumn('action', function ($object) {
				$result = '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a> ';
				if ($object->canEdit()) {
					$result .= '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canPay()) {
					$result .= '<a href="javascript:void(0)" title="Thanh toán nhanh" class="btn btn-sm btn-primary quick-pay"><i class="fas fa-hand-holding-usd"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->addIndexColumn()
			->rawColumns(['pay_status','action'])
			->make(true);
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		if (!$object->canEdit()) return view('not_found');
		return view($this->view.'.edit', compact(['object']));
	}

  public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'products' => 'required|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'supplier_id' => 'required|exists:suppliers,id',
			'pay_type' => 'required|in:1,2',
			'vat_percent' => 'required',
			'import_date' => 'required',
			'status' => 'required|in:1,3'
		];

		$translate = [
			'products.min' => 'Bắt buộc phải chọn',
			'products.*.qty.min' => 'Không hợp lệ',
			'products.*.qty.max' => 'Không hợp lệ',
			'products.*.qty.not_in' => 'Không hợp lệ',
		];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		if ($validate->fails()) return errorResponse("", $validate->errors());

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(8);
			$object->import_date = $request->import_date;
			$object->supplier_id = $request->supplier_id;
			$object->qty = $request->qty;
			$object->pay_type = $request->pay_type;
			$object->vat_percent = $request->vat_percent;
			$object->amount = $request->amount;
			$object->amount_after_vat = $request->amount_after_vat;
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;
			$object->status = $request->status;
			$object->save();

			$object->generateCode();

			$object->syncProducts($request->products);

			if ($object->status == ThisModel::DA_DUYET) $object->updateWarehouse();

			DB::commit();

			return successResponse("", $object);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}


	public function update(Request $request, $id)
	{
		$rule = [
			'products' => 'required|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'supplier_id' => 'required|exists:suppliers,id',
			'pay_type' => 'required|in:1,2',
			'vat_percent' => 'required|integer',
			'import_date' => 'required',
			'status' => 'required|in:1,3'
		];

		$translate = [
			'products.min' => 'Bắt buộc phải chọn',
			'products.*.qty.min' => 'Không hợp lệ',
			'products.*.qty.max' => 'Không hợp lệ',
			'products.*.qty.not_in' => 'Không hợp lệ',
		];


		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		$json = new stdClass();

		if ($validate->fails()) return errorResponse("", $validate->errors());

		DB::beginTransaction();
		try {
			$object = ThisModel::findOrFail($id);

			$object->import_date = $request->import_date;
			$object->supplier_id = $request->supplier_id;
			$object->qty = $request->qty;
			$object->pay_type = $request->pay_type;
			$object->vat_percent = $request->vat_percent;
			$object->amount = $request->amount;
			$object->amount_after_vat = $request->amount_after_vat;
			$object->note = $request->note;
			$object->status = $request->status;

			$object->save();

			$object->syncProducts($request->products);

			if ($object->status == ThisModel::DA_DUYET) $object->updateWarehouse();

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
			$object->delete();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		} else {
			$message = array(
				"message" => "Không thể xóa!",
				"alert-type" => "warning"
			);
		}

    	return redirect()->route($this->route.'.index')->with($message);
	}

	public function print($id, Request $request) {
        $object = ThisModel::where('id', $id)->firstOrFail();
		return $object->print();
    }
}
