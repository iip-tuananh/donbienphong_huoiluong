<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\G7Service as ThisModel;
use App\Model\Uptek\Service;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use App\Helpers\FileHelper;
use Illuminate\Validation\Rule;
use Auth;
use App\Model\Common\File;

class G7ServiceController extends Controller
{
	protected $view = 'g7.services';
	protected $route = 'G7Service';

	public function index()
	{
		return view($this->view.'.index');
	}

    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
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
				$result = '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->rawColumns(['action','image'])
			->addIndexColumn()
			->make(true);
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

	public function create()
	{
		return view($this->view.'.create');
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

	public function store(Request $request)
	{
		$rule = [
			'root_service_id' => 'required|exists:services,id',
			'products' => 'required|array|min:1',
			'products.*.g7_product_id' => 'required|exists:g7_products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_vehicle_categories' => 'required|array|min:1',
			'service_vehicle_categories.*.vehicle_category_id' => 'required|exists:vehicle_categories,id',
			'service_vehicle_categories.*.groups' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.name' => 'required|max:255',
			'service_vehicle_categories.*.groups.*.service_price' => 'required|numeric|min:0|max:999999999',
			'service_vehicle_categories.*.groups.*.products' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.products.*.g7_product_id' => 'required|exists:g7_products,id',
			'service_vehicle_categories.*.groups.*.products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'status' => 'required|in:1,0'
		];

		// if (!$request->root_service_id) {
		// 	$rule = array_merge($rule, [
		// 		'name' => [
		// 			'required',
		// 			Rule::unique('g7_services')->where(function ($query) use ($request) {
		// 				return $query->where('g7_id', Auth::user()->g7_id);
		// 			}),
		// 		],
		// 		'code' => [
		// 			'required',
		// 			Rule::unique('g7_services')->where(function ($query) use ($request) {
		// 				return $query->where('g7_id', Auth::user()->g7_id);
		// 			}),
		// 			'unique:services'
		// 		],
		// 		'service_type_id' => 'required|exists:service_types,id',
		// 	]);
		// }

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
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			if ($request->root_service_id) {
				$exist = ThisModel::where('root_service_id', $request->root_service_id)->where('g7_id', Auth::user()->g7_id)->first();

				if ($exist) {
					$json->success = false;
					$json->message = "Đã tồn tại trên hệ thống!";
					return Response::json($json);
				}

				$root_service = Service::find($request->root_service_id);
				$object->root_service_id = $request->root_service_id;
				$object->name = $root_service->name;
				$object->code = $root_service->code;
				$object->service_type_id = $root_service->service_type_id;
			} else {
				// $object->name = $request->name;
				// $object->code = $request->code;
				// $object->service_type_id = $request->service_type_id;
			}

			$object->status = $request->status;
			$object->g7_id = Auth::user()->g7_id;

			$object->save();

			FileHelper::uploadFile($request->image, 'services', $object->id, ThisModel::class, 'image',1);

			$object->syncVehicleCategories($request->service_vehicle_categories);
			$object->syncProducts($request->products);

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
		$object = ThisModel::findOrFail($id);

		$rule = [
			// 'root_service_id' => 'nullable|exists:services,id',
			'products' => 'required|array|min:1',
			'products.*.g7_product_id' => 'required|exists:g7_products,id',
			'products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'service_vehicle_categories.*.groups' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.name' => 'required|max:255',
			'service_vehicle_categories.*.groups.*.service_price' => 'required|numeric|min:0|max:999999999',
			'service_vehicle_categories.*.groups.*.products' => 'required|array|min:1',
			'service_vehicle_categories.*.groups.*.products.*.g7_product_id' => 'required|exists:g7_products,id',
			'service_vehicle_categories.*.groups.*.products.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'status' => 'required|in:1,0'
		];

		// if (!$request->root_service_id) {
		// 	$rule = array_merge($rule, [
		// 		'name' => [
		// 			'required',
		// 			Rule::unique('g7_services')->where(function ($query) use ($request) {
		// 				return $query->where('g7_id', Auth::user()->g7_id);
		// 			})->ignore($id),
		// 		],
		// 		'service_type_id' => 'required|exists:service_types,id',
		// 	]);
		// }

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
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}

		if ($request->status == 0 && !$object->canDelete()) {
			$json->success = false;
			$json->message = "Không thể khóa dịch vụ này!";
			return Response::json($json);
		}

		DB::beginTransaction();
		try {

			// if (!$object->root_service_id) {
			// 	$object->name = $request->name;
			// 	$object->code = $request->code;
			// 	$object->service_type_id = $request->service_type_id;
			// }

			$object->status = $request->status;
			$object->save();
			// FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
			if($request->image) {
				FileHelper::uploadFile($request->image, 'services', $object->id, ThisModel::class, 'image',1);
			}


			$object->syncVehicleCategories($request->service_vehicle_categories);
			$object->syncProducts($request->products);

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
			$object->status = 0;
			$object->save();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		}

        return redirect()->route($this->route.'.index')->with($message);
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

	public function getDataForBillNew(Request $request) {
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

	public function getDataForFilter(Request $request) {
        $json = new stdclass();
		if ($request->type_id == null) {
			$json->success = true;
			$json->data = ThisModel::getForSelect();
			return Response::json($json);
		}
        $json->success = true;
        $json->data = ThisModel::getDataForBillNew($request->type_id);
        return Response::json($json);
	}

}
