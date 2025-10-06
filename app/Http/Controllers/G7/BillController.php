<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\Bill as ThisModel;
use App\Model\Common\Car;
use App\Model\Common\Customer;
use App\Model\Uptek\Service;
use App\Model\Product;
use App\Model\G7\Stock;
use Yajra\DataTables\DataTables;
use Validator;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use App\Model\Uptek\AccumulatePoint;
use App\Model\Common\PromoCampaign;
use \Carbon\Carbon;
use DB;

class BillController extends Controller
{
  	protected $view = 'g7.bills';
	protected $route = 'Bill';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForReceipt($id)
	{
		$invoice = ThisModel::find($id);

		$json = new stdClass();
		if($invoice) {
			$json->success = true;
			$json->data = $invoice;
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Lấy dữ liệu thất bại";
			return Response::json($json);
		}
	}

	public function searchData(Request $request)
	{
		$objects = ThisModel::searchByFilter($request);
		return Datatables::of($objects)
			->editColumn('created_at', function ($object) {
				return Carbon::parse($object->created_at)->format("d/m/Y H:i");
			})
			->editColumn('bill_date', function ($object) {
				return $object->bill_date ? Carbon::parse($object->bill_date)->format("d/m/Y H:i") : '';
			})
			->editColumn('pay_status', function ($object) {
				if($object->payed_value >= $object->cost_after_vat) {
					$result = "<span class='badge badge-success'>Đã thanh toán</span>";
				} else {
					$result = "<span class='badge badge-warning'>Còn công nợ</span>";
				}
				if($object->point_pay > 0) {
					$result .= "</br><span style='font-style: italic; font-size: 12px'>Thanh toán sử dụng <b>". formatCurrency($object->point_pay) ." </b> điểm</span>";
				}
				if($object->promo_id) {
					$result .= "</br><span style='font-style: italic; font-size: 12px'>Sử dụng KM: <b>". PromoCampaign::where('id', $object->promo_id)->first()->name ." </b></span>";
				}
				return $result;
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name;
			})
			->editColumn('cost_after_vat', function ($object) {
				return formatCurrency($object->cost_after_vat);
			})
			->editColumn('status', function ($object) {
				return getStatus($object->status, ThisModel::STATUSES);
			})
			->addColumn('action', function ($object) {
				$result = "";
				if ($object->canView()) {
					$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a> ';
				}
				if ($object->canEdit()) {
					$result .= '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if($object->canPay()) {
					$result .= '<a href="javascript:void(0)" title="Tạo nhanh phiếu thu" class="btn btn-sm btn-primary quick-receipt"><i class="fas fa-hand-holding-usd"></i></a> ';
				}
				if($object->canExport()) {
					$result .= '<a href="' . route('WarehouseExport.create') . '?bill_id='.$object->id.'" title="Xuất kho" class="btn btn-sm btn-primary"><i class="fas fa-box-open"></i></a> ';
				}
				if($object->canReturn()) {
					$result .= '<a href="' . route('BillReturn.create') . '?bill_id='.$object->id.'" title="Tạo hóa đơn trả" class="btn btn-sm btn-primary"><i class="fas fa-undo"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Hủy" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;
			})
			->rawColumns(['code', 'status', 'action','pay_status'])
			->addIndexColumn()
			->make(true);
	}

	public function edit($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canEdit()) return view('not_found');
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

	public function checkPrint($id)
	{
		$object = ThisModel::where('id', $id)->with([
            'products',
            'services',
        ])
        ->firstOrFail();

        $products = $object->products;
        $services = $object->services;

        $data = $products->merge($services)->sortBy('index');
        dd($data);
	}

	public function getDataForShow($id)
	{
		$object = ThisModel::findOrFail($id);
		$json = new stdClass();
		if ($object->canView()) {
			$json->success = true;
			$json->data = ThisModel::getDataForShow($id);
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Lấy dữ liệu thất bại";
			return Response::json($json);
		}
	}

  	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function store(Request $request)
	{
		$rule = [
			'bill_date' => 'required|date',
			// 'car_id' => 'required|exists:cars,id',
			'customer_id' => 'required|exists:customers,id',
			// 'payment_method' => 'required|in:1,2',
			'status' => 'required|in:1,3',

			'list' => 'required|array|min:1',
			'list.*.service_id' => 'required_if:list.*.is_service,true|exists:services,id',
			'list.*.group_id' => 'required_if:list.*.is_service,true|exists:service_vehicle_category_groups,id',
			'list.*.product_id' => 'required_if:list.*.is_product,true|exists:products,id',
			'list.*.name' => 'required_if:list.*.is_other,true',
			'list.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'list.*.price' => 'required|numeric|min:0|max:999999999',
			'list.*.index' => 'required|numeric',

			'service_total_cost' => 'required|numeric|min:0|max:999999999999',
			'product_total_cost' => 'required|numeric|min:0|max:999999999999',
			'total_cost' => 'required|numeric|min:1|max:999999999999',
			'sale_cost' => 'required|numeric|min:0|max:999999999999',
			'cost_after_sale' => 'required|numeric|min:0|max:999999999999',
			'vat_percent' => 'required|numeric|min:0|max:99',
			'vat_cost' => 'required|numeric|min:0|max:999999999',
			'cost_after_vat' => 'required|numeric|min:0|max:999999999999',
		];

		$translate = [];

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

		$car = Car::find($request->car_id);

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->code = randomString(20);
			$object->bill_date = $request->bill_date;

			$object->car_id = $request->car_id;
			if ($object->car_id) {
				$object->license_plate = $car->license_plate;
				$object->vehicle_category_id = $car->category_id;
			}

			$object->customer_id = $request->customer_id;
			$customer = Customer::find($object->customer_id);
			$object->customer_name = $customer->name;

			$object->service_total_cost = $request->service_total_cost;
			$object->product_total_cost = $request->product_total_cost;
			$object->total_cost = $request->total_cost;
			$object->sale_cost = $request->sale_cost;
			$object->cost_after_sale = $request->cost_after_sale;
			$object->promo_id = $request->promo_id;
			$object->promo_value = $request->promo_value;
			$object->cost_after_promo = $request->cost_after_promo;
			$object->vat_percent = $request->vat_percent;
			$object->vat_cost = $request->vat_cost;
			$object->cost_after_vat = $request->cost_after_vat;

			$object->status = $request->status;
			$object->note = $request->note;
			$object->g7_id = Auth::user()->g7_id;

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approved_time = Carbon::now();
				if($request->allow_point === 'true') {
					// Thanh toán điểm thưởng
					if($customer->current_point < $request->points) {
						$json->success = false;
						$json->errors = $validate->errors();
						$json->message = "Điểm thanh toán không đủ!";
						return Response::json($json);
					} else {
						$point_money = $request->points * AccumulatePoint::getPointRate();
						$object->payed_value = $point_money;
						$object->point_pay = $request->points;
						$object->point_money = $point_money;
						// Trừ số điểm hiện tại của khách
						$customer->current_point = $customer->current_point - $request->points;
						$customer->save();
					}
				}
			}

			$object->save();
			$object->generateCode();

			$object->syncList($request->list);
			$object->syncPromoProducts($request->promo_products);

			// Cập nhật thông tin đăng kiểm, bảo hiểm xe
			if($request->update_reminder === 'true') {
				Car::where('id', $request->car_id)->update([
					'registration_deadline' => $request->registration_deadline,
					'hull_insurance_deadline' => $request->hull_insurance_deadline,
					'maintenance_dateline' => $request->maintenance_dateline,
					'insurance_deadline' => $request->insurance_deadline,
				]);
			}

			// Tính điểm tích lũy
			$points = ($object->cost_after_sale) / AccumulatePoint::where('id',1)->first()->value_to_point_rate;

			if ($object->status == ThisModel::DA_DUYET) {
				$customer->update(['accumulate_point' => $customer->accumulate_point + $points, 'current_point' => $customer->current_point + $points]);
				$object->approve();
			}

			$json->success = true;
			if ($object->status == ThisModel::DANG_TAO) {
				$json->message = 'Lưu thành công. Bạn cần duyệt để hóa đơn có hiệu lực';
			} else {
				$json->message = 'Thao tác thành công. Hóa đơn đã có hiệu lực';
			}
			$json->data = $object;
			DB::commit();
			return Response::json($json);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}


	public function update(Request $request, $id)
	{
		$json = new stdClass();
		$object = ThisModel::findOrFail($id);
		if (!$object->canEdit()) {
			$json->success = false;
			$json->message = "Không có quyền";
			return Response::json($json);
		}

		$rule = [
			'bill_date' => 'required|date',
			// 'car_id' => 'required|exists:cars,id',
			'customer_id' => 'required|exists:customers,id',
			// 'payment_method' => 'required|in:1,2',
			'status' => 'required|in:1,3',

			'list' => 'required|array|min:1',
			'list.*.service_id' => 'required_if:list.*.is_service,true|exists:services,id',
			'list.*.group_id' => 'required_if:list.*.is_service,true|exists:service_vehicle_category_groups,id',
			'list.*.product_id' => 'required_if:list.*.is_product,true|exists:products,id',
			'list.*.name' => 'required_if:list.*.is_other,true',
			'list.*.qty' => 'required|numeric|min:0|max:99999999|not_in:0',
			'list.*.price' => 'required|numeric|min:0|max:999999999',
			'list.*.index' => 'required|numeric',
			'service_total_cost' => 'required|numeric|min:0|max:999999999999',
			'product_total_cost' => 'required|numeric|min:0|max:999999999999',
			'total_cost' => 'required|numeric|min:1|max:999999999999',
			'sale_cost' => 'required|numeric|min:0|max:999999999999',
			'cost_after_sale' => 'required|numeric|min:0|max:999999999999',
			'vat_percent' => 'required|numeric|min:0|max:99',
			'vat_cost' => 'required|numeric|min:0|max:999999999',
			'cost_after_vat' => 'required|numeric|min:0|max:999999999999',
		];

		$translate = [];

		$validate = Validator::make(
			$request->all(),
			$rule,
			$translate
		);

		if ($validate->fails()) {
			$json->success = false;
			$json->errors = $validate->errors();
			$json->message = "Sửa thất bại!";
			return Response::json($json);
		}

		$car = Car::find($request->car_id);

		DB::beginTransaction();
		try {
			$object->bill_date = $request->bill_date;
			// $object->payment_method = $request->payment_method;

			$object->car_id = $request->car_id;
			if ($object->car_id) {
				$object->license_plate = $car->license_plate;
				$object->vehicle_category_id = $car->category_id;
			}

			$object->customer_id = $request->customer_id;
			$customer = Customer::find($object->customer_id);
			$object->customer_name = $customer->name;

			$object->service_total_cost = $request->service_total_cost;
			$object->product_total_cost = $request->product_total_cost;
			$object->total_cost = $request->total_cost;
			$object->sale_cost = $request->sale_cost;
			$object->cost_after_sale = $request->cost_after_sale;
			$object->promo_id = $request->promo_id;
			$object->promo_value = $request->promo_value;
			$object->cost_after_promo = $request->cost_after_promo;
			$object->vat_percent = $request->vat_percent;
			$object->vat_cost = $request->vat_cost;
			$object->cost_after_vat = $request->cost_after_vat;

			$object->status = $request->status;
			$object->note = $request->note;

			if ($object->status == ThisModel::DA_DUYET) {
				$object->approved_time = Carbon::now();
				if($request->allow_point === 'true') {

					// Thanh toán điểm thưởng
					if($customer->current_point < $request->points) {
						$json->success = false;
						$json->errors = $validate->errors();
						$json->message = "Điểm thanh toán không đủ!";
						return Response::json($json);
					} else {
						$point_money = $request->points * AccumulatePoint::getPointRate();
						// Check điểm thanh toán lớn hơn giá trị
						if($object->cost_after_vat < $point_money) {
							$json->success = false;
							$json->errors = $validate->errors();
							$json->message = "Số điểm thanh toán vượt giá trị đơn hàng!";
							return Response::json($json);
						}
						$object->payed_value = $point_money;
						$object->point_pay = $request->points;
						$object->point_money = $point_money;
					}
				}
			}
			$object->save();

			$object->syncList($request->list);
			$object->syncPromoProducts($request->promo_products);

			// Cập nhật thông tin đăng kiểm, bảo hiểm xe
			if($request->update_reminder === 'true') {
				Car::where('id', $request->car_id)->update([
					'registration_deadline' => $request->registration_deadline,
					'hull_insurance_deadline' => $request->hull_insurance_deadline,
					'maintenance_dateline' => $request->maintenance_dateline,
					'insurance_deadline' => $request->insurance_deadline,
				]);
			}

			// Tính điểm tích lũy
			$points = ($object->cost_after_sale) / AccumulatePoint::where('id',1)->first()->value_to_point_rate;
			$points_update = $points - ($request->points);

			if ($object->status == ThisModel::DA_DUYET) {
				// Cộng điểm tích lũy
				$customer->update(['accumulate_point' => $customer->accumulate_point + $points, 'current_point' => $customer->current_point + $points]);
				$object->approve();
			}
			$json->success = true;
			if ($object->status == ThisModel::DANG_TAO) {
				$json->message = 'Lưu thành công. Bạn cần duyệt để hóa đơn có hiệu lực';
			} else {
				$json->message = 'Thao tác thành công. Hóa đơn đã có hiệu lực';
			}
			$json->data = $object;
			DB::commit();
			return Response::json($json);
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception($e->getMessage());
		}
	}

	public function delete($id) {
		$object = ThisModel::findOrFail($id);
		if ($object->canDelete()) {
			$object->status = 0;
			$object->save();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		} else {
			$message = array(
				"message" => "Không thể hủy!",
				"alert-type" => "warning"
			);
		}
    	return redirect()->route($this->route.'.index')->with($message);
	}

	// Chưa check khuyến mãi
	// public function validateStock($list, $vehicle_category_id) {
	// 	$json = new stdClass();
	// 	$object = [];
	// 	foreach ($list as $item) {
	// 		if (isset($item['service_id'])) {
	// 			$service = Service::find($item['service_id']);
	// 			foreach ($service->getRecipeProducts($vehicle_category_id, $item['group_id']) as $key => $value) {
	// 				if (!isset($object[$key])) $object[$key] = 0;
	// 				$object[$key] += $value * $item['qty'];
	// 			}
	// 		} else {
	// 			if (!isset($object[$item['product_id']])) $object[$item['product_id']] = 0;
	// 			$object[$item['product_id']] += $item['qty'];
	// 		}
	// 	}
	// 	foreach ($object as $key => $value) {
	// 		$stock = Stock::where('g7_id', Auth::user()->g7_id)->where('product_id', $key)->first();
	// 		if (!$stock || $stock->qty < $value) {
	// 			$json->success = false;
	// 			$json->message = "Vật tư ".Product::find($key)->name." không đủ số lượng";
	// 			return $json;
	// 		}
	// 	}
	// 	$json->success = true;
	// 	return $json;
	// }

	public function getDataForFinalAdjust()
	{
		$json = new stdClass();
		$json->success = true;
		$json->data = ThisModel::getDataForFinalAdjust();
		$json->message = "Lấy dữ liệu thành công";
		return Response::json($json);
	}

	public function getDataForWarehouseExport($id)
	{
		return successResponse("", ThisModel::getDataForWarehouseExport($id));
	}

	public function getDataByCustomer(Request $request, $id)
	{
		$json = new stdClass();
		$json->success = true;
		$json->data = ThisModel::where('customer_id',$id)->orderBy('bill_date','desc')->get();
		$json->message = "Lấy dữ liệu thành công";
		return Response::json($json);
	}

	public function print($id, Request $request) {
        $object = ThisModel::where('id', $id)->firstOrFail();
		return $object->print($request->type);
    }

	public function getDataForReturn($id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canReturn()) return errorResponse("Không thể trả hàng cho hóa đơn này");
		return successResponse("", ThisModel::getDataForReturn($id));
	}

}
