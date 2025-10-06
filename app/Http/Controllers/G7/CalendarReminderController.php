<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\CalendarReminder as ThisModel;
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

class CalendarReminderController extends Controller
{
	protected $view = 'g7.calendar_reminders';
	protected $route = 'CalendarReminder';

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
			->editColumn('reminder_date', function ($object) {
				return formatDate($object->reminder_date);
			})
			->editColumn('type', function ($object) {
				return $object->getReminderType($object->reminder_type);
			})
			->editColumn('customer', function ($object) {
				return $object->customer->name ? $object->customer->name : '';
			})
			->editColumn('car', function ($object) {
				return $object->car->licensePlate->license_plate ? $object->car->licensePlate->license_plate : '';
			})
			->editColumn('created_at', function ($object) {
				return $object->user_create->name ? $object->user_create->name : '';
			})
			->addColumn('action', function ($object) {
				// $result = '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="fas fa-eye"></i></a> ';
				$result = '';
				if($object->canUpdate()) {
					$result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
					if($object->status == 1) {
						$result .= '<a href="javascript:void(0)" title="Cập nhật nhanh" class="btn btn-sm btn-info confirmed"><i class="far fa-calendar-check"></i></a> ';
					}
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
				'reminder_date' => 'required|date|after_or_equal:now',
				'reminder_type' => 'required|in: 1,2,3',
				'customer_id' => 'required|exists:customers,id',
				'car_id' => 'required|exists:cars,id',
				'note' => 'nullable|max:255',
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
			$object->reminder_date = $request->reminder_date;
			$object->reminder_type = $request->reminder_type;
			$object->customer_id = $request->customer_id;
			$object->car_id = $request->car_id;
			$object->note = $request->note;
			$object->status = 1;
			$object->g7_id = Auth::user()->g7_id;
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
				'reminder_date' => 'required|date|after_or_equal:now',
				'reminder_type' => 'required|in: 1,2,3',
				'customer_id' => 'required|exists:customers,id',
				'car_id' => 'required|exists:cars,id',
				'note' => 'nullable|max:255',

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
			$object->reminder_date = $request->reminder_date;
			$object->reminder_type = $request->reminder_type;
			$object->customer_id = $request->customer_id;
			$object->car_id = $request->car_id;
			$object->note = $request->note;
			$object->status = 1;
			$object->g7_id = Auth::user()->g7_id;
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


	public function getDataForEdit(Request $request, $id) {
		$object = ThisModel::getDataForEdit($id);
		$json = new stdclass();

		if($object) {
			$json->success = true;
			$json->status_code = 200;
			$json->message = "Lấy giữ liệu thành công !";
			$json->data = $object;

		} else {
			$json->success = false;
			$json->status_code = 400;
			$json->message = "Lấy giữ liệu thất bại !";
		}
		return Response::json($json);

	}

	public function conFirmed(Request $request, $id) {
		$object = ThisModel::find($id);
		$json = new stdclass();

		if($object) {
			$object->status = 2;
			$object->save();
			$json->success = true;
			$json->status_code = 200;
			$json->message = "Cập nhật thành công !";
			$json->data = $object;

		} else {
			$json->success = false;
			$json->status_code = 500;
			$json->message = "Có lỗi xảy ra !";
		}
		return Response::json($json);

	}

	// Xuất Excel
	public function exportExcel(Request $request)
	{
		return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_sach_nhac_lich.xlsx', function ($object) {
			return [
				'Khách hàng' => $object->customer->name,
				'Biển số xe' => $object->car->licensePlate->license_plate,
				'Thời gian đặt' => \Carbon\Carbon::parse($object->reminder_date)->format('d/m/Y'),
				'Loại nhắc lịch' => ThisModel::getReminderType($object->reminder_type),
				'Trạng thái' => $object->status == 1 ? 'Đang chờ' : 'Đã xử lý',
			];
		});
	}

	// Xuất PDF
	public function exportPDF(Request $request) {
		$data = ThisModel::searchByFilter($request);
		$pdf = PDF::loadView($this->view.'.pdf', compact('data'));
		return $pdf->download('danh_sach_nhac_lich.pdf');
	}
}
