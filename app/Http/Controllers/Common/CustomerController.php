<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\Customer as ThisModel;
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
use Illuminate\Validation\Rule;
use DateTime;
use App\Model\Common\CarCustomer;
use App\Model\Uptek\CustomerLevel;

class CustomerController extends Controller
{

	protected $view = 'common.customers';
	protected $route = 'Customer';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForShow($id)
	{
		$object = ThisModel::find($id);
		$json = new stdClass();

		if ($object) {
			$json->success = true;
			$json->data = ThisModel::getDataForShow($id);
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Không tồn tại !";
			return Response::json($json);
		}
	}

	public function getPoints($id)
	{
		$object = ThisModel::find($id);
		$json = new stdClass();

		if ($object) {
			$json->success = true;
			$json->data = ThisModel::where('id', $id)->first()->current_point;
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Không tồn tại !";
			return Response::json($json);
		}
	}

	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->editColumn('updated_at', function ($object) {
				return Carbon::parse($object->updated_at)->format("d/m/Y");
			})
			->editColumn('birth_day', function ($object) {
				return Carbon::parse($object->birth_day)->format("d/m/Y");
			})
			->editColumn('g7_id', function ($object) {

				if($object->g7_id) {
					return $object->g7Info->name;
				} else {
					return 'Uptek';
				}

			})
			->editColumn('updated_by', function ($object) {
				if($object->user_update) {
					return $object->user_update->name;
				} else {
					return "";
				}
			})
			->editColumn('full_adress', function ($object) {
                return $object->full_adress;
            })
			->addColumn('level', function ($object) {
				$level = CustomerLevel::where('point','<=', $object->accumulate_point)->orderBy('point','desc')->first();
				if($level) {
					return $level->name;
				} else {
					return "Khách phổ thông";
				}
            })
            ->addColumn('action', function ($object) {
				$result = '';
				if($object->canEdit()) {
					$result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="fas fa-eye"></i></a> ';
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
				'name' => [
					'required',
					Rule::unique('customers')->where(function ($query) use ($request) {
						return $query->where('mobile', $request->mobile);
					}),
				],
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'email' => 'nullable|email|unique:customers,email',
				'birthday' => 'nullable|before:tomorrow',
				'gender' => 'required|in: 0,1',
				'customer_group_id' => 'nullable|exists:customer_groups,id',
				'province_id' => 'nullable|exists:provinces,id',
				'district_id' => [
					'nullable',
					Rule::exists('districts','id')->where(function($query) use ($request) {
						$query->where('parent_code',$request->province_id);
					})
				],
				'ward_id' => [
					'nullable',
					Rule::exists('wards','id')->where(function($query) use ($request) {
						$query->where('parent_code', $request->district_id);
					})
				],
				'car_id' => 'nullable|exists:cars,id'
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

			// Tạo mã khách hàng
			$object->code = randomString(10);
			$object->mobile = $request->mobile;
			$object->email = $request->email;
			$object->gender = $request->gender;
			$object->birth_day = $request->birth_day;
			$object->customer_group_id = $request->customer_group_id;
			$object->province_id = $request->province_id;
			$object->district_id = $request->district_id;
			$object->ward_id = $request->ward_id;
			$object->adress = $request->adress;
			$object->g7_id = Auth::user()->g7_id;

			$object->save();
			$object->generateCode();

			if ($request->car_id) {
				$car_customer = new CarCustomer();
				$car_customer->car_id = $request->car_id;
				$car_customer->customer_id = $object->id;
				$car_customer->save();
			}

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

	public function edit($id)
	{
		$object = ThisModel::find($id);
		$json = new stdClass();

		if ($object->canEdit()) {
			$json->success = true;
			$json->data = ThisModel::getDataForEdit($id);
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Bạn không đủ quyền !";
			return Response::json($json);
		}
	}

	public function update(Request $request, $id)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'name' => [
					'required', 'max:180',
					Rule::unique('customers')->where(function ($query) use ($request) {
						return $query->where('mobile', $request->mobile);
					})->ignore($id),
				],
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'email' => 'email|unique:customers,email,'.$id,
				'birthday' => 'before:tomorrow',
				'gender' => 'required|in: 0,1',
				'customer_group_id' => 'required|exists:customer_groups,id',
				'province_id' => 'required|exists:provinces,id',
				'district_id' => [
					'required',
					Rule::exists('districts','id')->where(function($query) use ($request) {
						$query->where('parent_code',$request->province_id);
					})
				],
				'ward_id' => [
					'required',
					Rule::exists('wards','id')->where(function($query) use ($request) {
						$query->where('parent_code', $request->district_id);
					})
				]
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
			$version = $object->createVersion();

			$object->name = $request->name;
			$object->mobile = $request->mobile;
			$object->email = $request->email;
			$object->gender = $request->gender;
			$object->birth_day = $request->birth_day;
			$object->customer_group_id = $request->customer_group_id;
			$object->province_id = $request->province_id;
			$object->district_id = $request->district_id;
			$object->ward_id = $request->ward_id;
			$object->adress = $request->adress;
			$object->status = $request->status;

			$object->saveHistory($version->id);
            $object->save();

            if (!count($version->histories)) $version->delete();

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
    public function exportExcel(Request $request)
	{
        return (new FastExcel(ThisModel::searchByFilter($request)->get()))->download('danh_sach_khach_hang.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên khách' => $object->name,
                'Số điện thoại' => $object->mobile,
                'Địa chỉ' => $object->full_adress,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    // Xuất PDF
    public function exportPDF(Request $request) {
        $data = ThisModel::searchByFilter($request)->get();
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_khach_hang.pdf');
    }
}
