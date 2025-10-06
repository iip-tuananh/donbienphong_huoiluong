<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\VehicleManufact;
use App\Model\Common\VehicleType as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class VehicleTypeController extends Controller
{
	protected $view = 'common.vehicles.types';
	protected $route = 'VehicleType';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForEdit($id)
	{
		$object = ThisModel::getData($id);

		$json = new stdClass();
		if($object) {
			$json->success = true;
			$json->messages = "Lấy dữ liệu thành công";
			$json->data = $object;
			return Response::json($json);
		} else {
			$json->success = false;
			$json->messages = "Không tồn tại đối tượng";
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
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name;
			})
            ->editColumn('manufacts', function ($object) {
                return $object->manufact->name;
            })
			->editColumn('category', function ($object) {
                return $object->category->name;
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
				'name' => 'required|unique:vehicle_types,name',
                'vehicle_manufact_id' => 'required|exists:vehicle_manufacts,id',
                'vehicle_category_id' => 'required|exists:vehicle_categories,id',
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
            $object->vehicle_manufact_id = $request->vehicle_manufact_id;
            $object->vehicle_category_id = $request->vehicle_category_id;
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
				'name' => 'required|unique:vehicle_manufacts,name,'.$id,
				'vehicle_manufact_id' => 'required|exists:vehicle_manufacts,id',
				'vehicle_category_id' => 'required|exists:vehicle_categories,id',
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
				$json->message = "Không thể khóa hãng này!";
				return Response::json($json);
			}
			$object->name = $request->name;
			$object->vehicle_manufact_id = $request->vehicle_manufact_id;
			$object->vehicle_category_id = $request->vehicle_category_id;
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
				"message" => "Không thế xóa!",
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
        return (new FastExcel(ThisModel::all()))->download('danh_sach_loai_xe.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên hãng' => $object->manufact->name,
                'Tên loại' => $object->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    // Xuất PDF
    public function exportPDF() {
        $data = ThisModel::all();
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_loai_xe.pdf');
    }
}
