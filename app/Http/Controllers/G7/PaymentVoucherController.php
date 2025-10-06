<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\G7\PaymentVoucher as ThisModel;
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
use App\Model\G7\G7FixedAssetImport;
use App\Model\G7\Supplier;
use App\Model\G7\WareHouseImport;

class PaymentVoucherController extends Controller
{
	protected $view = 'g7.funds.payment_vouchers';
	protected $route = 'PaymentVoucher';

	public function index()
	{
		return view($this->view.'.index');
	}

	public function getDataForShow($id)
	{
		$invoice = ThisModel::where('id', $id)
		->with([
			'recipientale',
			'paymentVoucherType',
			'user_create',
			'recipientale',
			'wareHouseImport',
			'g7FixedAssetImport'
		])->firstOrFail();

		$recipient_types = ThisModel::RECIPIENT_TYPES;
		foreach($recipient_types as $val) {
			if($val['id'] == $invoice->recipient_type_id) {
				$recipient_type_name = $val['name'];
			}
		}

		$json = new stdClass();
		if($invoice) {
			$json->success = true;
			$json->data = $invoice;
			$json->recipient_type_name = $recipient_type_name;
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
			->editColumn('value', function ($object) {
				return $object->value ? formatCurrent($object->value, 0, '', ',') : '';
			})
			->editColumn('payment_voucher_type_id', function ($object) {
				return $object->paymentVoucherType->name;
			})
			->editColumn('recipient_type_id', function ($object) {
				return $object->getRecipientType($object->recipient_type_id);
			})
			->addColumn('recipient_name', function ($object) {
				if($object->recipient_type_id  == 4) {
					return $object->recipient_name;
				} else {
					return $object->recipientale->name;
				}
			})

			->addColumn('action', function ($object) {
				$result = "";
				$result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn btn-sm btn-info show"><i class="far fa-eye"></i></a> ';
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
		// dd($request->all());
		$validate = Validator::make(
			$request->all(),
			[
				'payment_voucher_type_id' => 'required|exists:payment_voucher_types,id',
				'record_date' => 'required',
				'recipient_type_id' => 'required|in:1,2,3,4',
				'recipient_name' => 'required_if:recipient_type_id,4|max:255',
				'supplier_id' => 'required_if:payment_voucher_type_id,1|exists:suppliers,id',
				'ware_house_import_id' => 'required_if:payment_voucher_type_id,1|exists:ware_house_imports,id',
				'recipient_name' => 'required_if:recipient_type_id,4|max:255',
				'value' => 'required|integer',
				'pay_type' => 'required|in:1,2',
				'note' => 'max:255',
				'month' => 'nullable|required_if:payment_voucher_type_id,5|integer',
				'g7_fixed_asset_import_id' => 'nullable|required_if:payment_voucher_type_id,2|exists:g7_fixed_asset_imports,id',
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

			$object->payment_voucher_type_id = $request->payment_voucher_type_id;
			$object->code = randomString(8);
			$object->record_date = $request->record_date;
			$object->recipient_type_id = $request->recipient_type_id;
			$object->recipient_name = $request->recipient_name;


			$object->pay_type = $request->pay_type;
			$object->month = $request->month;

			if($request->recipient_type_id == 1) {
				$model = Customer::class;
				$object->recipient_name = Customer::find($request->recipient_id)->name;
			}
			else if($request->recipient_type_id == 2) {
				$model = User::class;
				$object->recipient_name = User::find($request->recipient_id)->name;
			}
			else if($request->recipient_type_id == 3) {
				$model = Supplier::class;
				$object->recipient_name = Supplier::find($request->recipient_id)->name;
			}
			else {
				$model = null;
				$object->recipient_name = $request->recipient_name;
			}

			$object->value = $request->value;
			$object->note = $request->note;
			$object->recipientale_id = $request->recipient_id;
			$object->supplier_id = $request->supplier_id;
			$object->ware_house_import_id = $request->ware_house_import_id;
			$object->g7_fixed_asset_import_id = $request->g7_fixed_asset_import_id;

			$object->recipientale_type = $model;

			$object->g7_id = Auth::user()->g7_id;

			$object->save();
			$object->generateCode();

			// Cập nhật số tiền đã thanh toán vào phiếu nhập hàng hóa
			if($object->ware_house_import_id) {
				$warehouse_import = WareHouseImport::find($object->ware_house_import_id);
				$payed_value = $warehouse_import->payed_value + $object->value;
				$object->wareHouseImport()->update(['payed_value' => $payed_value]);
			}

			// Cập nhật số tiền đã thanh toán vào phiếu nhập TSCĐ
			if($object->g7_fixed_asset_import_id) {
				$fixed_asset_import = G7FixedAssetImport::find($object->g7_fixed_asset_import_id);
				$payed_value = $fixed_asset_import->payed_value + $object->value;
				$object->g7FixedAssetImport()->update(['payed_value' => $payed_value]);
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

	public function getRecipient($type_id)
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
}
