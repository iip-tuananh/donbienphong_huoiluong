<?php

namespace App\Http\Controllers\Uptek;

use Illuminate\Http\Request;
use App\Model\Uptek\G7Info as ThisModel;
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

class G7InfoController extends Controller
{
	protected $view = 'uptek.g7_infos';
	protected $route = 'G7Info';

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
			->editColumn('full_adress', function ($object) {
				return $object->full_adress;
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
				'name' => 'required|unique:products,name',
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/|unique:g7_infos,mobile',
				'status' => 'required|in:0,1',
				'email' => 'nullable|email',
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
				],
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
			$object->province_id = $request->province_id;
			$object->district_id = $request->district_id;
			$object->ward_id = $request->ward_id;
			$object->adress = $request->adress;

			$object->note = $request->note;
			$object->status = $request->status;
			$object->save();

			FileHelper::uploadFile($request->image, 'g7_infos', $object->id, ThisModel::class, 'image');

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
				'name' => 'required|unique:products,name,'.$id,
				'mobile' => 'required|regex:/^(0)[0-9]{9,11}$/|unique:g7_infos,mobile,'.$id,
				'status' => 'required|in:0,1',
				'email' => 'nullable|email',
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
				],
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
			$object->mobile = $request->mobile;
			$object->email = $request->email;
			$object->province_id = $request->province_id;
			$object->district_id = $request->district_id;
			$object->ward_id = $request->ward_id;
			$object->adress = $request->adress;

			$object->note = $request->note;
			$object->status = $request->status;
			$object->save();

			if($request->image) {
				FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
				FileHelper::uploadFile($request->image, 'products', $object->id, ThisModel::class, 'image');
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
        return (new FastExcel(ThisModel::all()))->download('danh_sach_ho_so_g7.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên gara' => $object->name,
                'Địa chỉ' => $object->full_adress,
                'Số điện thoại' => $object->mobile,
                'Ghi chú' => $object->note,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

	// Xuất PDF
    public function exportPDF() {
        $data = ThisModel::all();
		PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_gara_g7.pdf');
    }

	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}
}
