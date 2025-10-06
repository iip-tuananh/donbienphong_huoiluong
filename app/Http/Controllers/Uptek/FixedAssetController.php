<?php

namespace App\Http\Controllers\Uptek;

use Illuminate\Http\Request;
use App\Model\Uptek\FixedAsset as ThisModel;
use App\Model\Common\Unit;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use Auth;
use App\Helpers\FileHelper;
use App\Model\Uptek\FixedAsset;

class FixedAssetController extends Controller
{
	protected $view = 'common.fixed_assets';
	protected $route = 'FixedAsset';

	public function index()
	{
		return view($this->view.'.index');
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
				return $object->user_create->name ? $object->user_create->name : '';
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name ? $object->user_update->name : '';
			})
			->addColumn('unit', function ($object) {
					return $object->unit ? $object->unit->name : '';
			})
			->addColumn('action', function ($object) {
				$result = '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->addIndexColumn()
			->make(true);
    }

	public function store(Request $request)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'name' => 'required|unique:fixed_assets,name',
				'unit_id' => 'required|exists:units,id',
				'import_price_quota' => 'required',
				'depreciation_period' =>'required|integer',
				'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000'

			]
		);
		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->name = $request->name;
			$object->code = randomString(6);
			$object->unit_id = $request->unit_id;
			$object->unit_name = Unit::find($request->unit_id)->name;
			$object->import_price_quota = $request->import_price_quota;
			$object->depreciation_period = $request->depreciation_period;
			$object->note = $request->note;
			$object->status = 1;
			$object->g7_id = Auth::user()->g7_id;
			$object->save();

			// Tạo lại code
			$object->code = 'TS'.'.'.generateCode(5,$object->id);
			$object->save();

			FileHelper::uploadFile($request->image, 'fixed_assets', $object->id, ThisModel::class, 'image',2);


			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			return Response::json($json);
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function update(Request $request, $id)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'name' => 'required|unique:fixed_assets,name,'.$id,
				'unit_id' => 'required|exists:units,id',
				'import_price_quota' => 'required|between:0,99999999999999',
				'depreciation_period' =>'required|integer',
				'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000'
			]
		);

		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}

		DB::beginTransaction();
		try {

			$object = ThisModel::findOrFail($id);
			if ($request->status == 0 && !$object->canDelete()) {
				$json->success = false;
				$json->message = "Không thể khóa dòng xe này!";
				return Response::json($json);
			}

			$object->name = $request->name;
			$object->unit_id = $request->unit_id;
			$object->unit_name = Unit::find($request->unit_id)->name;
			$object->status = $request->status;
			$object->import_price_quota = $request->import_price_quota;
			$object->depreciation_period = $request->depreciation_period;
			$object->note = $request->note;
			$object->save();

			if($request->image) {
				FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
				FileHelper::uploadFile($request->image, 'products', $object->id, ThisModel::class, 'image',2);
			}

			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			return Response::json($json);
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function delete($id)
    {
		$object = ThisModel::findOrFail($id);
		if (!$object->canDelete()) {
			$message = array(
				"message" => "Không thể xóa!",
				"alert-type" => "warning"
			);
		} else {
			$object->delete();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		}


        return redirect()->route($this->route.'.index')->with($message);
	}

	public function getDataForG7(Request $request, $id) {
		$json = new stdClass();

		$object = ThisModel::findOrFail($id);
		$can_g7_use = $object->canG7Use();
		if (!$can_g7_use->success) {
			return Response::json($can_g7_use);
		}

		$json->success = true;
		$json->data = ThisModel::getDataForG7($id);

		return Response::json($json);
	}
		// Xuất Excel
		public function exportExcel(Request $request)
		{
			return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_muc_tai_san_co_dinh.xlsx', function ($object) {
				return [
					'Tên' => $object->name,
					'Đơn vị tính' => $object->unit_name,
					'Giá định mức' => formatCurrency($object->import_price_quota),
					'Thời gian khấu hao' => $object->depreciation_period,
					'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
				];
			});
		}

		// Xuất PDF
		public function exportPDF(Request $request) {
			$data = ThisModel::searchByFilter($request);
			$pdf = PDF::loadView($this->view.'.pdf', compact('data'));
			return $pdf->download('danh_muc_tai_san_co_dinh.pdf');
		}
}
