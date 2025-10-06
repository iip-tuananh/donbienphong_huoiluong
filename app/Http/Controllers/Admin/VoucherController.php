<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Admin\Voucher as ThisModel;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use \stdClass;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    protected $view = 'admin.vouchers';
    protected $route = 'vouchers';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('from_date', function ($object) {
                return formatDate($object->from_date);
            })
            ->editColumn('to_date', function ($object) {
                return formatDate($object->to_date);
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger delete"><i class="fas fa-times"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action'])
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
                'code' => 'required|max:255|unique:vouchers,code',
                'status' => 'required',
                'quantity' => 'required',
                'value' => 'required',
                'description' => 'required',
                'content' => 'required',
                'from_date' => 'required|date',
                'to_date' => 'required|date'
            ]
        );
        $json = new stdClass();

        if ($validate->fails()) {
            $json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
        }

        $store_data = $request->all();

        DB::beginTransaction();
        try {
            $object = new ThisModel();
            $object->fill($store_data);
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

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'code' => 'required|unique:vouchers,code,' . $id,
                'status' => 'required',
                'quantity' => 'required',
                'value' => 'required',
                'description' => 'required',
                'content' => 'required',
                'from_date' => 'required|date',
                'to_date' => 'required|date'
            ]
        );
        $json = new stdClass();

        if ($validate->fails()) {
            $json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
        }
        $store_data = $request->all();

        DB::beginTransaction();
        try {
            $object = ThisModel::findOrFail($id);
            $object->fill($store_data);

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

        $object->delete();

        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );


        return redirect()->route($this->route . '.index')->with($message);
    }

    public function getDataForEdit($id)
    {
        $data = ThisModel::getDataForEdit($id);
        $json = new stdclass();
        $json->success = true;
        $json->data = $data;
        return Response::json($json);
    }
}
