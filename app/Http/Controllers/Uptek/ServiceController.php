<?php

namespace App\Http\Controllers\Uptek;

use Illuminate\Http\Request;
use App\Model\Uptek\Service as ThisModel;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Validator;
use App\Model\Common\User;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use App\Model\Common\ServiceType;
use App\Model\Common\VehicleCategory;
use App\Model\G7\BillService;
use App\Helpers\FileHelper;

class ServiceController extends Controller
{
  	protected $view = 'uptek.services';
	protected $route = 'Service';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function show($id)
	{
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.show', compact(['object']));
	}

	// Hàm lấy data cho bảng list
	public function searchData(Request $request)
	{
		$objects = ThisModel::searchByFilter($request);
		return Datatables::of($objects)
			->editColumn('name', function ($object) {
				return "<a title='Xem chi tiết' href='".route('Service.show', $object->id)."'>".$object->name."</a>";
			})
			->editColumn('updated_at', function ($object) {
				return Carbon::parse($object->updated_at)->format("d/m/Y");
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name;
			})
			->editColumn('service_type', function ($object) {
				return $object->service_type->name;
			})
			->editColumn('image', function ($object) {
				return ($object->image) ? "<img src=". $object->image->path ." style='max-width: 55px !important'>" : '-';
			})
			->addColumn('action', function ($object) {
				$result = '';
				if($object->canEdit()) {
					$result .= '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->addIndexColumn()
			->rawColumns(['action','image','name'])
			->make(true);
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

  public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'products' => 'nullable|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_vehicle_categories' => 'required|array|min:1',
			'service_vehicle_categories.*.vehicle_category_id' => 'required|exists:vehicle_categories,id',
			'service_vehicle_categories.*.groups' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.name' => 'required|max:255',
			'service_vehicle_categories.*.groups.*.service_price' => 'required|numeric|min:0|max:999999999',
			'service_vehicle_categories.*.groups.*.products' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.products.*.product_id' => 'required|exists:products,id',
			'service_vehicle_categories.*.groups.*.products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_type_id' => 'required|exists:service_types,id',
			'name' => 'required|max:255|unique:services,name',
			'image' => 'required|file|mimes:jpg,jpeg,png|max:3000'
		];

		$translate = [
			'products.min' => 'Bắt buộc phải chọn',
			'products.*.qty.min' => 'Không hợp lệ',
			'products.*.qty.max' => 'Không hợp lệ',
			'products.*.qty.not_in' => 'Không hợp lệ',
		];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
			$json->errors = $validate->errors();
			$json->message = "Tạo thất bại!";
			return Response::json($json);
		}

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->name = $request->name;
			$object->code = randomString(20);
			$object->service_type_id = $request->service_type_id;
			$object->save();

			$object->generateCode();

			$object->syncVehicleCategories($request->service_vehicle_categories);
			$object->syncProducts($request->products);
			FileHelper::uploadFile($request->image, 'services', $object->id, ThisModel::class, 'image',1);

			DB::commit();
			$json->success = true;
			$json->message = 'Tạo thành công';
			return Response::json($json);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}


	public function update(Request $request, $id)
	{
		$rule = [
			'products' => 'nullable|array|min:1',
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_vehicle_categories' => 'required|array|min:1',
			'service_vehicle_categories.*.vehicle_category_id' => 'required|exists:vehicle_categories,id',
			'service_vehicle_categories.*.groups' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.name' => 'required|max:255',
			'service_vehicle_categories.*.groups.*.service_price' => 'required|numeric|min:0|max:999999999',
			'service_vehicle_categories.*.groups.*.products' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.products.*.product_id' => 'required|exists:products,id',
			'service_vehicle_categories.*.groups.*.products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_type_id' => 'required|exists:service_types,id',
			'name' => 'required|max:255|unique:services,name,'.$id,
			'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000'
		];

		$translate = [
			'products.min' => 'Bắt buộc phải chọn',
			'products.*.qty.min' => 'Không hợp lệ',
			'products.*.qty.max' => 'Không hợp lệ',
			'products.*.qty.not_in' => 'Không hợp lệ',

		];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
			$json->errors = $validate->errors();
			$json->message = "Sửa thất bại!";
			return Response::json($json);
		}

		$object = ThisModel::findOrFail($id);

		$group_ids = [];
		foreach ($request->service_vehicle_categories as $svc) {
			foreach ($svc['groups'] as $g) {
				if (isset($g['id'])) array_push($group_ids, $g['id']);
			}
		}

		foreach ($object->service_vehicle_category_groups as $g) {
			if (!in_array($g->id, $group_ids)) {
				$used = BillService::where('group_id', $g->id)->first();
				if ($used) {
					$json->success = false;
					$json->message = "Nhóm ".$g->name." đã được đại lý G7 sử dụng. Không thể xóa";
					return Response::json($json);
				}
			}
		}

		DB::beginTransaction();
		try {
			$object->name = $request->name;
			$object->service_type_id = $request->service_type_id;
			$object->save();
			$object->syncVehicleCategories($request->service_vehicle_categories);
			$object->syncProducts($request->products);

			if ($request->image) {
				FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
				FileHelper::uploadFile($request->image, 'services', $object->id, ThisModel::class, 'image',1);
			}

			DB::commit();
			$json->success = true;
			$json->message = 'Sửa thành công';
			return Response::json($json);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function delete($id) {
		$object = ThisModel::findOrFail($id);
		$object->status = 0;
		$object->save();
		$message = array(
			"message" => "Thao tác thành công!",
			"alert-type" => "success"
		);
    	return redirect()->route($this->route.'.index')->with($message);
	}


	public function searchDataForBill(Request $request)
    {
		$objects = ThisModel::searchDataForBill($request);

        return Datatables::of($objects)
			->editColumn('price', function ($object) {
				return formatCurrency($object->price);
			})
			->addIndexColumn()
			->make(true);
    }

	public function getDataForBill(Request $request) {
        $json = new stdclass();
		if (!$request->detail_id) {
			$json->success = false;
			$json->message = "Thiếu thông tin";
			return Response::json($json);
		}
        $json->success = true;
        $json->data = ThisModel::getDataForBill($request->detail_id);
        return Response::json($json);
	}

	public function searchAllForBill(Request $request) {
        $json = new stdclass();
		if (!$request->vehicle_category_id) {
			$json->success = false;
			$json->message = "Thiếu thông tin";
			return Response::json($json);
		}
        $json->success = true;
        $json->data = ThisModel::searchAllForBill($request->vehicle_category_id);
        return Response::json($json);
	}
}
