<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\ReceiptVoucher as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use App\Model\Common\Customer;
use \Carbon\Carbon;
use DB;
use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response as FacadesResponse;
use DateTime;
use App\Model\Common\User;
use App\Model\G7\Supplier;
use App\Model\G7\Bill;
use phpDocumentor\Reflection\Types\This;

class ReceiptVoucherController extends Controller
{
	protected $view = 'g7.funds.receipt_vouchers';
	protected $route = 'ReceiptVoucher';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForShow($id)
	{
		$invoice = ThisModel::where('id', $id)
		->with([
			'payer',
			'receiptVoucherType',
			'user_create',
			'bill',
		])->firstOrFail();

		$payer_types = ThisModel::PAYER_TYPES;
		foreach($payer_types as $val) {
			if($val['id'] == $invoice->payer_type_id) {
				$payer_type_name = $val['name'];
			}
		}

		$json = new stdClass();
		if($invoice) {
			$json->success = true;
			$json->data = $invoice;
			$json->payer_type_name = $payer_type_name;
			$json->message = "Lấy dữ liệu thành công";
			return Response::json($json);
		} else {
			$json->success = false;
			$json->message = "Lấy dữ liệu thất bại";
			return Response::json($json);
		}
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
			->editColumn('payer_type_id', function ($object) {
				return $object->getPayerType($object->payer_type_id);
			})
			->addColumn('payer_name', function ($object) {
				if($object->payer_type_id  == 4) {
					return $object->payer_name;
				} else {
					return $object->payer->name;
				}
			})
			->editColumn('value', function ($object) {
				return $object->value ? formatCurrent($object->value, 0, '', ',') : '';
			})

			->addColumn('action', function ($object) {
				$result = '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a> ';
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
				'receipt_voucher_type_id' => 'required|exists:receipt_voucher_types,id',
				'record_date' => 'required',
				'payer_type_id' => 'required|in:1,2,3,4',
				'payer_id' => 'required_if:payer_type_id,1,2,3|integer',
				'payer_name' => 'required_if:payer_type_id,4|max:255',
				'customer_id' => 'required_if:payer_type_id,1|exists:customers,id',
				'bill_id' => 'required_if:payer_type_id,1|exists:bills,id',
				'payer_name' => 'required_if:payer_type_id,4|max:255',
				'value' => 'required|integer',
				'pay_type' => 'required|in:1,2',
				'note' => 'max:255',
			]
		);
		$json = new stdClass();

		if ($validate->fails()) {
			$json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
		}
		// if($request->payer_type_id == 1 && $request->bill_id) {
		// 	$bill = Bill::find($request->bill_id);
		// 	if($bill) {
		// 		if(($bill->payed_value + $request->value) > $bill->cost_after_vat) {
		// 			$json->success = false;
		// 			$json->message = "Thanh toán vượt giá trị hóa đơn!";
		// 			return Response::json($json);
		// 		}
		// 	} else {
		// 		$json->success = false;
		// 		$json->message = "Hóa đơn không tồn tại!";
		// 		return Response::json($json);
		// 	}
		// }

		DB::beginTransaction();
		try {
			$object = new ThisModel;

			$object->receipt_voucher_type_id = $request->receipt_voucher_type_id;
			$object->code = randomString(8);
			$object->record_date = $request->record_date;
			$object->payer_type_id = $request->payer_type_id;
			$object->pay_type = $request->pay_type;

			if($request->payer_type_id == 1) {
				$model = Customer::class;
				$object->payer_name = Customer::find($request->payer_id)->name;
			}
			else if($request->payer_type_id == 2) {
				$model = User::class;
				$object->payer_name = User::find($request->payer_id)->name;
			}
			else if($request->payer_type_id == 3) {
				$model = Supplier::class;
				$object->payer_name = Supplier::find($request->payer_id)->name;
			}
			else {
				$model = null;
				$object->payer_name = $request->payer_name;
			}

			$object->value = $request->value;
			$object->note = $request->note;
			$object->payer_id = $request->payer_id;
			$object->customer_id = $request->customer_id;
			$object->bill_id = $request->bill_id;

			$object->payer_type = $model;

			$object->g7_id = Auth::user()->g7_id;

			$object->save();
			$object->generateCode();

			// Cập nhật số tiền đã thanh toán vào hóa đơn bán
			if($object->bill_id) {
				$payed_value = Bill::find($object->bill_id);
				$payed_value = $payed_value->payed_value + $object->value;
				$object->bill()->update(['payed_value' => $payed_value]);
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
                'Tên' => $object->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

	public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}

	public function getPayer($type_id)
	{
		$json = new stdclass();
		$json->success = true;
		$objects = null;

		if($type_id == 1) {
			$objects = \App\Model\Common\Customer::getForSelect();
		}

		if($type_id == 2) {
			$objects = \App\Model\Common\User::where('g7_id', Auth::user()->g7_id)->get();
		}

		if($type_id == 3) {
			$objects = \App\Model\G7\Supplier::getForSelect();
		}

		$json->data = $objects;

		return Response::json($json);
	}

    public function print($id) {
        $voucher = ThisModel::where('id', $id)->firstOrFail();
        // if (!Auth::user()->canViewQuotation($quotation)) return view('not_found');
        $template = $voucher->print_template;
        return view('print', compact('template'));
    }
}
