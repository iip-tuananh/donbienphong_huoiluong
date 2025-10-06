<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\Booking as ThisModel;
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
use App\Model\Common\Customer;

class BookingController extends Controller
{
	protected $view = 'common.bookings';
	protected $route = 'Booking';

	public function index()
	{
		return view($this->view.'.index');
	}

	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->editColumn('customer', function ($object) {
				return $object->customer->name;
			})
			->addColumn('mobile', function ($object) {
				return $object->customer->mobile;
			})
			->editColumn('booking_time', function ($object) {
				return Carbon::parse($object->booking_time)->format("H:m d/m/Y");
			})
			->addColumn('g7', function ($object) {
				return $object->g7Info->name;
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

	public function create()
	{
		return view($this->view.'.create');
	}

	public function store(Request $request)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'customer_id' => 'required|exists:customers,id',
				'status' => 'required|in:0,1',
				'g7_id' => [
					Rule::requiredIf(function () use ($request) {
						return Auth::user()->type == 1;
					}),
					'exists:g7_infos,id',
				],
				'note' => 'nullable|max:255',
				'booking_time' => 'required'

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
			$object->customer_id = $request->customer_id;
			$object->mobile = Customer::find($request->customer_id)->mobile;
			$object->note = $request->note;
			if(Auth::user()->type == 1 && $request->g7_id) {
				$object->g7_id = $request->g7_id;
			} else {
				$object->g7_id = Auth::user()->g7_id;
			}

			$object->booking_time = $request->booking_time;
			$object->status = $request->status;
			$object->save();

			if ($request->status == 1) $object->send();

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

	public function show(Request $request,$id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canview()) return view('not_found');
		$object = ThisModel::getDataForShow($id);
		return view($this->view.'.show', compact('object'));
	}

	public function update(Request $request, $id)
	{

		$validate = Validator::make(
			$request->all(),
			[
				'customer_id' => 'required|exists:customers,id',
				'status' => 'required|in:0,1',
				'g7_id' => [
					Rule::requiredIf(function () use ($request) {
						return Auth::user()->type == 1;
					}),
					'exists:g7_infos,id',
				],
				'note' => 'nullable|max:255',
				'booking_time' => 'required'

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
			$object->customer_id = $request->customer_id;
			$object->mobile = Customer::find($request->customer_id)->mobile;
			$object->note = $request->note;
			if(Auth::user()->type == 1 && $request->g7_id) {
				$object->g7_id = $request->g7_id;
			} else {
				$object->g7_id = Auth::user()->g7_id;
			}
			$object->booking_time = $request->booking_time;
			$object->status = $request->status;
			$object->save();

			if ($request->status == 1) $object->send();

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
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
	}


		// Xuất Excel
		public function exportExcel(Request $request)
		{
			return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_sach_lich_hen.xlsx', function ($object) {
				return [
					'Khách hàng' => $object->customer->name,
					'SĐT khách' => $object->customer->mobile,
					'Giờ hẹn' => \Carbon\Carbon::parse($object->booking_time)->format('H:m d/m/Y'),
					'Ghi chú' => $object->note,
					'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
				];
			});
		}

		// Xuất PDF
		public function exportPDF(Request $request) {
			$data = ThisModel::searchByFilter($request);
			$pdf = PDF::loadView($this->view.'.pdf', compact('data'));
			return $pdf->download('danh_sach_lich_hen.pdf');
		}
}
