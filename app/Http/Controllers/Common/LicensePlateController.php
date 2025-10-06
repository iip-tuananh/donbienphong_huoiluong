<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\LicensePlate as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class LicensePlateController extends Controller
{
	protected $view = 'common.license_plates';
	protected $route = 'LicensePlate';

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
				'license_plate' => 'required|unique:license_plates,license_plate|max:10',
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
			$object->license_plate = $request->license_plate;
			$object->status = 1;
			$object->save();

			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			$json->data = $object;
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
				'license_plate' => 'required|max:10|unique:license_plates,license_plate,'.$id,
				'status' => 'required|in:0,1'
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
				$json->message = "Không thể khóa!";
				return Response::json($json);
			}
			$object->license_plate = $request->license_plate;
			$object->status = $request->status;
			$object->save();

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

	// Xuất Excel
    public function exportExcel() {
        return (new FastExcel(ThisModel::all()))->download('danh_sach_vat_tu.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên' => $object->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}
}
