<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Order;
use App\Model\Admin\Post;
use App\Model\Admin\Rent;
use App\Model\Common\Customer;
use Illuminate\Http\Request;
use App\Model\Common\Customer as ThisModel;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use \stdClass;

use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected $view = 'admin.customers';
    protected $route = 'customers';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = Customer::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('created_at', function ($object) {
                return \Illuminate\Support\Carbon::parse($object->created_at)->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';
                $result = $result . ' <a href="'.route('customers.show', $object->id).'" title="Xem chi tiết" class="dropdown-item"><i class="fa fa-angle-right"></i>Xem chi tiết</a>';
                $result = $result . '</div></div>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['code', 'customer', 'variant', 'approve_rental_price', 'action'])
            ->make(true);
    }
    public function searchDataPost(Request $request)
    {
        $objects = Customer::searchPostOfCustomer($request);
        return Datatables::of($objects)
            ->addColumn('customer', function ($object) {
                $name = data_get($object, 'customer.fullname', '');
                $code = data_get($object, 'customer.code', '');
                return '<a href="'.route('customers.show', $object->customer_id).'">' . e($name) . ' (' . e($code) . ')</a>';
            })
            ->editColumn('code', function ($object) {
                return '<a href = "'.route('orders.show', $object->id).'" title = "Xem chi tiết">' . $object->code . '</a>';
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ?? '_';
            })
            ->editColumn('status', function ($object) {
                $map = [
                    'pending'  => ['label' => 'Chờ duyệt', 'class' => 'badge bg-warning text-dark'],
                    'approved' => ['label' => 'Đã duyệt',  'class' => 'badge bg-success'],
                    'rejected' => ['label' => 'Hủy',   'class' => 'badge bg-danger'],
                ];
                $key = (string) ($object->status ?? '');
                $cfg = $map[$key] ?? ['label' => 'Không rõ', 'class' => 'badge bg-secondary'];

                return '<span class="'.$cfg['class'].'">'.e($cfg['label']).'</span>';
            })
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format('d/m/Y H:i');
            })
            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';
                $result = $result . ' <a href="'.route('orders.show', $object->id).'" title="đổi trạng thái" class="dropdown-item"><i class="fa fa-angle-right"></i>Xem chi tiết</a>';
//                $result = $result . ' <a href="'.route('orders.delete', $object->id).'" title="xóa" class="dropdown-item confirm"><i class="fa fa-angle-right"></i>Xóa</a>';

                $result = $result . '</div></div>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['code', 'action', 'customer', 'status'])
            ->make(true);
    }

    public function show(Request $request, $id) {
        $object = Customer::query()->with(['avatar'])->withCount('posts')->find($id);

        $object->date_join = Carbon::parse($object->created_at)->format('d/m/Y H:i');

        return view($this->view . '.show', compact('object'));
    }

    public function updateStatus(Request $request)
    {
        $order = Customer::query()->find($request->rent_id);

        $order->status = $request->status;
        $order->save();

        return Response::json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    }

    public function update($id, Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'status' => 'required',
            ]
        );
        $json = new stdClass();

        if ($validate->fails()) {
            $json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Bắt buộc nhập trạng thái!";
            return Response::json($json);
        }

        $order = Customer::query()->find($id);
        $order->status = $request->status;
        $order->save();

        return Response::json(['success' => true, 'message' => 'Thao tác thành công']);
    }
}
