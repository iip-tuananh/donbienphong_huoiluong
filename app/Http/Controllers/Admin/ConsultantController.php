<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileHelper;
use App\Model\Admin\Consultant;
use Illuminate\Http\Request;
use App\Model\Admin\Consultant as ThisModel;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use \stdClass;

use Rap2hpoutre\FastExcel\FastExcel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConsultantController extends Controller
{
    protected $view = 'admin.consultants';
    protected $route = 'consultants';

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
            ->rawColumns(['action'])
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
                'name' => 'required|max:255|unique:consultants,name',
                'phone' => 'required',
                'email' => 'required',
                'role' => 'required',
                'gender' => 'required',
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
            $object->phone = $request->phone;
            $object->email = $request->email;
            $object->role = $request->role;
            $object->gender = $request->gender;
            $object->created_by = auth()->id();
            $object->save();

            if($request->image) {
                FileHelper::uploadFile($request->image, 'consulants', $object->id, Consultant::class, 'image',99);
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
                'name' => 'required|unique:consultants,name,' . $id,
                'phone' => 'required',
                'email' => 'required',
                'role' => 'required',
                'gender' => 'required',
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
            $object->name = $request->name;
            $object->phone = $request->phone;
            $object->email = $request->email;
            $object->role = $request->role;
            $object->gender = $request->gender;
            $object->created_by = auth()->id();
            $object->save();

            if($request->image) {
                if($object->image) {
                    FileHelper::forceDeleteFiles($object->image->id, $object->id, Consultant::class, 'image');
                }
                FileHelper::uploadFile($request->image, 'consulants', $object->id, ThisModel::class, 'image');
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
        if (!$object->canEdit()) {
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

    public function getDataForEdit($id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }
}
