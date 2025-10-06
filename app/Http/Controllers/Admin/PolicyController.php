<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Category;
use Illuminate\Http\Request;
use App\Model\Admin\Policy as ThisModel;
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

class PolicyController extends Controller
{
    protected $view = 'admin.policies';
    protected $route = 'policies';

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
            ->editColumn('status', function ($object) {
                return $object->status == 1 ? 'Xuất bản' : 'Nháp';
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="' . route($this->route . '.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a> ';
                $result .= '<a href="' . route($this->route . '.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['link', 'action', 'image'])
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
                'title' => 'required',
                'status' => 'required',
                'content' => 'required',
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
            $object->content = $request->content;
            $object->status = $request->status;
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

    public function show(Request $request, $id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view . '.show', compact('object'));
    }

    public function edit(Request $request, $id)
    {
        $object = ThisModel::getDataForEdit($id);

        return view($this->view . '.edit', compact('object'));
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'status' => 'required',
                'content' => 'required',
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
            $object->content = $request->content;
            $object->status = $request->status;

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


}
