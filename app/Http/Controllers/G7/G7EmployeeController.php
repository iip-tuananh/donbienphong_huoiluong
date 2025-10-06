<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\G7Employee as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use DB;
use Auth;

class G7EmployeeController extends Controller
{
	protected $view = 'g7.g7_employees';
	protected $route = 'G7Employee';

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
				$result = '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="fas fa-eye"></i></a> ';
				if($object->canEdit()) {
					$result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
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
				'name' => 'required|max:255',
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'status' => 'required|in:0,1',
				'email' => 'required|email',
				'address' => 'required|max:255',
				'gender' => 'required|in:0,1',
				'image' => 'required|file|mimes:jpg,jpeg,png|max:3000'

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
			$object->mobile = $request->mobile;
			$object->email = $request->email;
			$object->start_date = $request->start_date;
			$object->birth_day = $request->birth_day;
			$object->address = $request->address;
			$object->gender = $request->gender;
			$object->g7_id = Auth::user()->g7_id;

			$object->status = $request->status;
			$object->save();

			FileHelper::uploadFile($request->image, 'g7_employees', $object->id, ThisModel::class, 'image',1);

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
				'name' => 'required|max:255',
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'status' => 'required|in:0,1',
				'email' => 'required|email',
				'address' => 'required|max:255',
				'gender' => 'required|in:0,1',
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
			$object->name = $request->name;
			$object->mobile = $request->mobile;
			$object->email = $request->email;
			$object->start_date = $request->start_date;
			$object->birth_day = $request->birth_day;
			$object->address = $request->address;
			$object->gender = $request->gender;
			$object->g7_id = Auth::user()->g7_id;

			$object->status = $request->status;
			$object->save();


			if($request->image) {
				FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
				FileHelper::uploadFile($request->image, 'g7_employees', $object->id, ThisModel::class, 'image',2);
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

	// Xuất Excel
    public function exportExcel() {
        return (new FastExcel(ThisModel::all()))->download('danh_sach_vat_tu.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên dòng xe' => $object->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
	}
}
