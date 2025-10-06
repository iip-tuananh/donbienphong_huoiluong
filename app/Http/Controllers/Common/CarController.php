<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\Car as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use Illuminate\Validation\Rule;
use DateTime;
use Auth;
use App\Model\Common\VehicleType;

class CarController extends Controller
{
	protected $view = 'common.cars';
	protected $route = 'Car';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForShow($id)
	{
		$json = new stdClass();
		$json->success = true;
		$json->data = ThisModel::getData($id);
		$json->message = "Lấy dữ liệu thành công";
		return Response::json($json);

	}

	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->addColumn('license_plate', function ($object) {
				return $object->license_plate ? $object->license_plate : '';
			})
			->editColumn('updated_at', function ($object) {
				return Carbon::parse($object->updated_at)->format("d/m/Y");
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name;
			})
			->addColumn('customers', function ($object) {
				$result = '';
                foreach ($object->customers as $customer) {
					$result .= '<span> - '.$customer->name.'</span><br/>';
				}
				return $result;
            })
			->editColumn('category', function ($object) {
                return $object->category->name;
            })
			->editColumn('manufact', function ($object) {
                return $object->manufact->name;
            })
			->editColumn('type', function ($object) {
                return $object->type->name;
            })
			->editColumn('category_id', function ($object) {
                return $object->category_id;
            })
			->editColumn('manufact_id', function ($object) {
                return $object->manufact_id;
            })
			->editColumn('type_id', function ($object) {
                return $object->type_id;
            })
			->addColumn('customer_values', function ($object) {
                return DB::table('car_customer')->where('car_id',$object->id)->pluck('customer_id')->toArray();
            })
            ->addColumn('action', function ($object) {
				$result = '';
				$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="fas fa-eye"></i></a> ';
				if($object->canEdit()) {
					$result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canDelete() && $object->status == 1) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;

            })
            ->addIndexColumn()
			->rawColumns(['customers','action'])
            ->make(true);
    }

	public function store(Request $request)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'license_plate' => 'required|max:10|unique:license_plates,license_plate',
				'customer_ids' => 'required|exists:customers,id',
				// 'category_id' => 'required|exists:vehicle_categories,id',
				'manufact_id' => 'required|exists:vehicle_manufacts,id',
				'type_id' => [
					'required',
					Rule::exists('vehicle_types','id')->where(function($query) use ($request) {
						$query->where('vehicle_manufact_id',$request->manufact_id);
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
			$object = new ThisModel();
			$object->license_plate = $request->license_plate;
			$object->note = $request->note;

			$object->manufact_id = $request->manufact_id;
			$object->type_id = $request->type_id;
			$object->category_id = VehicleType::find($request->type_id)->category->id;

			// Reminder infos
			$object->registration_deadline = $request->registration_deadline;
			$object->hull_insurance_deadline = $request->hull_insurance_deadline;
			$object->maintenance_dateline = $request->maintenance_dateline;
			$object->insurance_deadline = $request->insurance_deadline;

			if(Auth::user()->type == 3 ) {
				$object->g7_id = Auth::user()->g7_id;
			}

			$object->save();
			$object->customers()->sync($request->customer_ids);

			DB::commit();
			$json->success = true;
			$json->data = $object;
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
				'customer_ids' => 'required|exists:customers,id',
				// 'category_id' => 'required|exists:vehicle_categories,id',
				'manufact_id' => 'required|exists:vehicle_manufacts,id',
				'type_id' => [
					'required',
					Rule::exists('vehicle_types','id')->where(function($query) use ($request) {
						$query->where('vehicle_manufact_id',$request->manufact_id);
					})
				],
				// 'status' => "required|in:0,1"
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

			// $object->license_plate = $request->license_plate;
			$object->note = $request->note;

			$object->manufact_id = $request->manufact_id;
			$object->type_id = $request->type_id;
			$object->category_id = VehicleType::find($request->type_id)->category->id;

			// Reminder infos
			$object->registration_deadline = $request->registration_deadline;
			$object->hull_insurance_deadline = $request->hull_insurance_deadline;
			$object->maintenance_dateline = $request->maintenance_dateline;
			$object->insurance_deadline = $request->insurance_deadline;

			$object->save();

			$object->customers()->sync($request->customer_ids);

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
				"message" => "Xóa thành công!",
				"alert-type" => "success"
			);
		}

        return redirect()->route($this->route.'.index')->with($message);
	}


	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}

	public function getCustomers($id)
	{
		$json = new stdClass();

		$object = ThisModel::find($id);
		if($object) {
			$json->status_code = 200;
			$json->success = true;
			$json->data = $object->customers;
			$json->messages = "Lấy dữ liệu thành công !";
			return Response::json($json);
		} else {
			$json->status_code = 500;
			$json->data = $object->customers;
			$json->success = false;
			$json->messages = "Lấy dữ liệu thất bại !";
			return Response::json($json);
		}
	}


	// Xuất Excel
    public function exportExcel() {
        return (new FastExcel(ThisModel::all()))->download('danh_sach_xe.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Biển số' => $object->licensePlate->license_plate,
                'Chủ sở hữu' => $object->customers[0]->name,
                'Dòng xe' => $object->category->name,
                'Hãng sx' => $object->manufact->name,
                'Loại xe' => $object->type->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

	    // Xuất PDF
    public function exportPDF() {
        $data = ThisModel::all();
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_xe.pdf');
    }

	public function searchForSelect(Request $request) {
		$data = ThisModel::select(['id', 'license_plate as name'])
			->where('license_plate', $request->license_plate)
			->get();
		return successResponse("", $data, ['draw' => $request->draw]);
    }
}
