<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin\ServiceType as ThisModel;
use Yajra\DataTables\DataTables;
use Response;
use Validator;
use \stdClass;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use DB;
use Auth;

class ServiceTypeController extends Controller
{
    protected $view = 'admin.service_types';
	protected $route = 'service_types';

	public function index(Request $request)
	{
        $type = $request->type ?? 1;
        if ($type == ThisModel::TRAI_NGHIEM) {
            $title = 'trải nghiệm';
        } elseif ($type == ThisModel::UU_DAI) {
            $title = 'ưu đãi';
        } elseif ($type == ThisModel::GOI_NGHI_DUONG) {
            $title = 'gói nghỉ dưỡng';
        } else {
            $title = '';
        }

		return view($this->view.'.index', compact('type', 'title'));
	}

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->editColumn('name', function ($object) {
				return $object->name;
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name ? $object->user_create->name : '';
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name ? $object->user_update->name : '';
			})

			->addColumn('action', function ($object) {
				$result = '';
				if($object->canEdit()) {
					$result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->addIndexColumn()
			->rawColumns(['name','action'])
			->make(true);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:service_types,name',
            'name_eng' => 'required|string|max:255|unique:service_types,name_eng',
        ]);
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
            $object->name_eng = $request->name_eng;
            $object->status = $request->status;
            $object->type = $request->type;
            $object->save();
            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (\Exception $e) {
            DB::rollBack();
            $json->success = false;
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
        }
    }

    public function edit($id)
    {
        $object = ThisModel::findOrFail($id);

        $json = new stdClass();
        $json->success = true;
        $json->data = $object;
        $json->message = "Thao tác thành công!";
        return Response::json($json);
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:service_types,name,'.$id,
            'name_eng' => 'required|string|max:255|unique:service_types,name_eng,'.$id,
        ]);
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
            $object->name = $request->name;
            $object->name_eng = $request->name_eng;
            $object->status = $request->status;
            $object->save();
            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (\Exception $e) {
            DB::rollBack();
            $json->success = false;
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
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
}
