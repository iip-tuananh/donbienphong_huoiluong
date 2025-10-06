<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\Supplier as ThisModel;
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

class SupplierController extends Controller
{
	protected $view = 'g7.suppliers';
	protected $route = 'Supplier';

	public function index()
	{
		return view($this->view.'.index');
	}
	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->editColumn('created_by', function ($object) {
				return $object->user_create->name ? $object->user_create->name : '';
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name ? $object->user_update->name : '';
			})
			->addColumn('action', function ($object) {
				$result = '';
				$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="fas fa-eye"></i></a> ';
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
				'name' => [
					'required',
					Rule::unique('suppliers')->where(function ($q) {
						return $q->where('g7_id', \App\Model\Common\User::find(Auth::user()->id)->g7Info->id);
					})
				],
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'status' => 'required|in:0,1',
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
			$object = new ThisModel;
			$object->code = randomString(6);
			$object->name = $request->name;
			$object->mobile = $request->mobile;
			$object->status = $request->status;
			$object->address = $request->address;

			$object->g7_id = Auth::user()->g7_id;

			$object->save();

			// Tạo lại code đúng
			$stt = ThisModel::where('g7_id', Auth::user()->g7Info->id)->count();
			$object->generateCode($stt+1);

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
				'name' => 'required_if:type,1|unique:fund_accounts,name',
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/',
				'status' => 'required|in:0,1',
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

			$object->name = $request->name;
			$object->mobile = $request->mobile;
			$object->status = $request->status;
			$object->address = $request->address;

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

	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}

	public function getDataForEdit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		$json = new stdClass();
		if($object) {
			$json->data = $object;
			$json->messages = "Lấy dữ liệu thành công !";
			$json->success = true;
			return Response::json($json);
		} else
		{
			$json->messages = "Có lỗi xảy ra !";
			$json->success = false;
			return Response::json($json);
		}
	}

		// Xuất Excel
		public function exportExcel(Request $request)
		{
			return (new FastExcel(ThisModel::searchByFilter($request)->get()))->download('danh_sach_nha_cung_cap.xlsx', function ($object) {
				return [
					'ID' => $object->id,
					'Tên nhà cung cấp' => $object->name,
					'Số điện thoại' => $object->mobile,
					'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
				];
			});
		}

		// Xuất PDF
		public function exportPDF(Request $request) {
			$data = ThisModel::searchByFilter($request)->get();
			$pdf = PDF::loadView($this->view.'.pdf', compact('data'));
			return $pdf->download('danh_sach_nha_cung_cap.pdf');
		}

}
