<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin\ProductRate as ThisModel;
use Yajra\DataTables\DataTables;
use \stdClass;
use Illuminate\Support\Facades\Response;

class ProductRateController extends Controller
{
    protected $view = 'admin.product_rates';
    protected $route = 'product_rates';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('created_at', function ($object) {
                return formatDate($object->created_at);
            })
            ->editColumn('updated_at', function ($object) {
                return $object->updated_at != $object->created_at ? formatDate($object->updated_at) : '';
            })
            ->editColumn('user_name', function ($object) {
                return $object->name . ' - ' . $object->phone . ' - ' . $object->email;
            })
            ->editColumn('product_name', function ($object) {
                return $object->product->name;
            })
            ->editColumn('status', function ($object) {
                return getStatus($object->status, ThisModel::STATUSES);
            })

            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';
                $result = $result . ' <a href="javascript:void(0)" title="đổi trạng thái" class="dropdown-item update-status"><i class="fa fa-angle-right"></i>Duyệt</a>';
                $result = $result . ' <a href="javascript:void(0)" title="xem chi tiết" class="dropdown-item edit"><i class="fa fa-angle-right"></i>Xem chi tiết</a>';
                $result = $result . '</div></div>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'status'])
            ->make(true);
    }

    public function getDataForEdit($id)
    {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }

    public function updateStatus(Request $request)
    {
        $productRate = ThisModel::query()->find($request->product_rate_id);

        $productRate->status = $request->status;
        $productRate->save();

        return Response::json(['success' => true, 'message' => 'Cập nhật trạng thái đánh giá thành công']);
    }
}
