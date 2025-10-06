<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Model\Admin\ConfigStatistic as ThisModel;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use \stdClass;

use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConfigStatisticController extends Controller
{
    protected $view = 'admin.config_static';
    protected $route = 'configStatistic';

    public function index()
    {
        return view($this->view.'.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ?: '';
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_update->name ?: '';
            })
            ->editColumn('updated_at', function ($object) {
                return formatDate($object->updated_at);
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['image','name','action'])
            ->make(true);
    }

    public function create()
    {
        return view($this->view.'.create');
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:255',
                'des' => 'required',
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

            $object->title = $request->title;
            $object->des = $request->des;
            $object->created_by = auth()->id();
            $object->save();

            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            $json->data = $object;
            return Response::json($json);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function show(Request $request,$id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view.'.show', compact('object'));
    }

    public function update(Request $request, $id)
    {

        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:255',
                'des' => 'required',
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
            $object->title = $request->title;
            $object->des = $request->des;
            $object->created_by = auth()->id();
            $object->save();

            $object->save();

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
        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );

        $object->delete();
        return redirect()->route($this->route.'.index')->with($message);
    }

    public function getDataForEdit($id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }

    // Xuất Excel
    public function exportExcel(Request $request)
    {
        return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_sach_lich_hen.xlsx', function ($object) {
            return [
                'Khách hàng' => $object->customer->name,
                'SĐT khách' => $object->customer->mobile,
                'Giờ hẹn' => \Carbon\Carbon::parse($object->booking_time)->format('H:m d/m/Y'),
                'Ghi chú' => $object->note,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    // Xuất PDF
    public function exportPDF(Request $request) {
        $data = ThisModel::searchByFilter($request);
        $pdf = \Barryvdh\DomPDF\PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_lich_hen.pdf');
    }
}
