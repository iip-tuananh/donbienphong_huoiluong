<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Model\Admin\DesignOrder as ThisModel;

class DesignOrderController extends Controller
{
    protected $view = 'admin.design_orders';
    protected $route = 'design_orders';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->addColumn('image_front', function ($object) {
                return '<img src="'.$object->image_front->path.'" alt="Ảnh mặt trước" class="img-fluid" "max-width: 55px !important">';
            })
            ->addColumn('image_back', function ($object) {
                return '<img src="'.$object->image_back->path.'" alt="Ảnh mặt sau" class="img-fluid" style="max-width: 55px !important">';
            })
            ->editColumn('created_at', function ($object) {
                return formatDate($object->created_at);
            })
            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';
                $result = $result . ' <a href="" title="đổi trạng thái" class="dropdown-item update-status"><i class="fa fa-angle-right"></i>Đổi trạng thái</a>';
                $result = $result . ' <a href="'.route('design_orders.show', $object->id).'" title="đổi trạng thái" class="dropdown-item"><i class="fa fa-angle-right"></i>Xem chi tiết</a>';
                $result = $result . '</div></div>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['image_front', 'image_back', 'action'])
            ->make(true);
    }

    public function show(Request $request, $id) {
        $order = ThisModel::query()->with(['details', 'details.image', 'image_front', 'image_back'])->find($id);
        $group_front_layers = $order->details->where('group', 1)->values();
        $group_back_layers = $order->details->where('group', 2)->values();

        $front_layers = [
            'image' => $order->image_front->path,
            'layers' => $group_front_layers,
        ];
        $back_layers = [
            'image' => $order->image_back->path,
            'layers' => $group_back_layers,
        ];
        return view($this->view . '.show', compact('order', 'front_layers', 'back_layers'));
    }
}
