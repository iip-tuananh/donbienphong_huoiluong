<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Category;
use App\Model\Admin\Manufacturer;
use Illuminate\Http\Request;
use App\Model\Admin\Manufacturer as ThisModel;
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

class ManufacturerController extends Controller
{
    protected $view = 'admin.manufacturers';
    protected $route = 'manufacturers';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ?? '';
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_update->name ?? '';
            })
            ->editColumn('updated_at', function ($object) {
                return formatDate($object->updated_at);
            })
            ->editColumn('products', function ($object) {
                return '<button class="btn btn-info btn-sm btn-branch" type="button">
                        ' . $object->products_count. '
                </button>';
            })
            ->addColumn('image', function ($object) {
                return '<img class="thumbnail img-preview" src="'.($object->image ? $object->image->path : '').'">';
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'image', 'products'])
            ->make(true);
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'code' => 'required|max:255|unique:manufacturers,code',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000',
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

            $object->name = $request->name;
            $object->code = $request->code;
            $object->created_by = auth()->id();
            $object->save();

            if($request->image) {
                FileHelper::uploadFile($request->image, 'manufacturers', $object->id, ThisModel::class, 'image',8);
            }

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

    public function show(Request $request, $id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view . '.show', compact('object'));
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'code' => 'required|unique:manufacturers,code,' . $id,
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000',
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
            $object->code = $request->code;
            $object->name = $request->name;

            $object->save();

            if($request->image) {
                if($object->image) {
                    FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
                }
                FileHelper::uploadFile($request->image, 'manufacturers', $object->id, ThisModel::class, 'image',8);
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


        return redirect()->route($this->route . '.index')->with($message);
    }

    public function getDataForEdit($id)
    {
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
    public function exportPDF(Request $request)
    {
        $data = ThisModel::searchByFilter($request);
        $pdf = \Barryvdh\DomPDF\PDF::loadView($this->view . '.pdf', compact('data'));
        return $pdf->download('danh_sach_lich_hen.pdf');
    }

    // xóa nhiều
    public function checkActDelete(Request $request) {
        $manu_ids = explode(',', $request->manu_ids);
        $check = true;

        foreach ($manu_ids as $manu_id) {
            $manu = Manufacturer::query()->where('id', $manu_id)->first();
            if(! $manu->canDelete()) {
                $check = false;
                break;
            }
        }

        return response()->json(['check' => $check]);
    }

    public function actDelete(Request $request) {
        $manu_ids = explode(',', $request->manu_ids);

        Manufacturer::query()->whereIn('id', $manu_ids)->delete();

        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );

        return redirect()->route($this->route.'.index')->with($message);
    }
}
