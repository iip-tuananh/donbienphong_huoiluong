<?php

namespace App\Http\Controllers\Admin;

use App\Mail\OrderActive;
use App\Model\Admin\ArticleSubmission;
use App\Model\Admin\Config;
use App\Model\Admin\Order;
use App\Model\Admin\OrderDetail;
use Illuminate\Http\Request;
use App\Model\Admin\ArticleSubmission as ThisModel;
use Illuminate\Support\Facades\Mail;
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
use App\Model\Common\Customer;

class OrderController extends Controller
{
    protected $view = 'admin.orders';
    protected $route = 'orders';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
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
        $order = ArticleSubmission::query()->with(['customer'])->find($id);

        return view($this->view . '.show', compact('order'));
    }

    public function delete($id) {
        $order = Order::query()->where('id', $id)->first();
        $order->details()->delete();

        $order->delete();

        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );

        return redirect()->route($this->route.'.index')->with($message);
    }

    public function updateStatus(Request $request)
    {
        $obj = ArticleSubmission::query()->find($request->article_id);

        $obj->status = $request->status;

        if($request->send == 2) {
            $obj->is_comment = 1;
            $obj->review_note = $request->review_note;
        }

        $obj->updated_by = Auth::user()->id;

        $obj->save();

        return Response::json(['success' => true, 'message' => 'cập nhật trạng thái thành công']);
    }
}
