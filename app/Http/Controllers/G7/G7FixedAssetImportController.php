<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\G7FixedAssetImport as ThisModel;
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

class G7FixedAssetImportController extends Controller
{
  	protected $view = 'g7.g7_fixed_asset_imports';
	protected $route = 'G7FixedAssetImport';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForShow($id)
	{
		$object = ThisModel::getDataForShow($id);

		if ($object) return successResponse("Lấy dữ liệu thành công", $object);
		else return errorResponse("Lấy dữ liệu thất bại");
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
			->editColumn('status', function ($object) {
				return getStatus($object->status, ThisModel::STATUSES);
			})
			->editColumn('supplier', function ($object) {
				return $object->supplier ? $object->supplier->name : "";
			})
			->editColumn('amount', function ($object) {
				return formatCurrent($object->amount);
			})
			->editColumn('vat_cost', function ($object) {
				return formatCurrent($object->vat_cost);
			})
			->editColumn('amount_after_vat', function ($object) {
				return formatCurrent($object->amount_after_vat);
			})
			->addColumn('action', function ($object) {
				$result = '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a> ';
				if ($object->canEdit()) {
					$result .= '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if($object->canPay()) {
					$result .= '<a href="javascript:void(0)" title="Tạo nhanh phiếu chi" class="btn btn-sm btn-primary quick-pay"><i class="fas fa-hand-holding-usd"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}

				return $result;
			})
			->addIndexColumn()
			->rawColumns(['status','action'])
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
			'details' => 'required|array|min:1',
			'details.*.asset_id' => 'required|exists:g7_fixed_assets,id',
			'details.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'details.*.price' => 'required|numeric|min:0|max:99999999|not_in:0',
			'supplier_id' => 'required|exists:suppliers,id',
			'vat_percent' => 'required',
			'import_date' => 'required',
			'status' => 'required|in:1,3'
		];

		$validate = Validator::make(
			$request->all(),
			$rule
		);

		if ($validate->fails()) return errorResponse("", $validate->errors());

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(8);
			$object->import_date = $request->import_date;
			$object->supplier_id = $request->supplier_id;
			$object->vat_percent = $request->vat_percent;
			$object->amount = $request->amount;
			$object->vat_cost = round($object->amount * $object->vat_percent / 100);
			$object->amount_after_vat = $object->amount + $object->vat_cost;
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;
			$object->status = $request->status;
			$object->save();

			$object->generateCode();

			$object->syncDetails($request->details);

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
			'details' => 'required|array|min:1',
			'details.*.asset_id' => 'required|exists:g7_fixed_assets,id',
			'details.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'details.*.price' => 'required|numeric|min:0|max:99999999|not_in:0',
			'supplier_id' => 'required|exists:suppliers,id',
			'vat_percent' => 'required',
			'import_date' => 'required',
			'status' => 'required|in:1,3'
		];


		$validate = Validator::make(
			$request->all(),
			$rule
		);

		$json = new stdClass();

		if ($validate->fails()) return errorResponse("", $validate->errors());

		DB::beginTransaction();
		try {
			$object = ThisModel::findOrFail($id);

			$object->import_date = $request->import_date;
			$object->supplier_id = $request->supplier_id;
			$object->vat_percent = $request->vat_percent;
			$object->amount = $request->amount;
			$object->vat_cost = round($object->amount * $object->vat_percent / 100);
			$object->amount_after_vat = $object->amount + $object->vat_cost;
			$object->note = $request->note;
			$object->status = $request->status;

			$object->save();

			$object->syncDetails($request->details);

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

	public function print($id) {
        $object = ThisModel::where('id', $id)->firstOrFail();
		return $object->print();
    }

}
