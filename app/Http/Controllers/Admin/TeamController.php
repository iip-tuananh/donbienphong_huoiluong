<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Admin\Team as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use DB;
use Auth;
use App\Model\Common\Customer;

class TeamController extends Controller
{
    protected $view = 'admin.teams';
    protected $route = 'teams';

    public function index()
    {
        return view($this->view.'.index');
    }
    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('name', function ($object) {
                return $object->name;
            })
            ->addColumn('message', function ($object) {
                return $object->message;
            })
            ->addColumn('image', function ($object) {
                return '<img style ="max-width:45px !important" src="' . ($object->image->path ?? '') . '"/>';
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ? $object->user_update->name : '';
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
            ->rawColumns(['name','image','action'])
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
                'name' => 'required|max:255',
                'phone_number' => 'required',
                'position' => 'nullable|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            $object->position = $request->position;
            $object->phone_number = $request->phone_number;
            $object->facebook = $request->facebook;
            $object->ins = $request->ins;
            $object->tw = $request->tw;
            $object->pri = $request->pri;

            $object->save();

            if ($request->image) {
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
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
                'name' => 'required|max:255',
                'phone_number' => 'required',
                'position' => 'nullable|max:255',
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
            $object->name = $request->name;
            $object->phone_number = $request->phone_number;
            $object->position = $request->position;
            $object->facebook = $request->facebook;
            $object->ins = $request->ins;
            $object->tw = $request->tw;
            $object->pri = $request->pri;

            $object->save();

            if ($request->image) {
                if ($object->image) {
                    // FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                // FileHelper::uploadFile($request->image, 'reviews', $object->id, ThisModel::class, 'image', 2);
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
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
            if($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
            }
            $object->delete();
            $message = array(
                "message" => "Thao tác thành công!",
                "alert-type" => "success"
            );
        }


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
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_lich_hen.pdf');
    }
}
