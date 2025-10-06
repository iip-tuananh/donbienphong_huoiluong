<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\Role as ThisModel;
use App\Model\Common\User;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Validator;
use App\Employee;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;

class RoleController extends Controller
{
	protected $view = 'common.roles';
	protected $route = 'Role';

	public function index()
	{
		return view($this->view.'.index');
	}

	// Hàm phân trang, search cho datatable
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
			->editColumn('updated_by', function ($object) {
				return $object->user_update->fullname;
			})
			->editColumn('name', function ($object) {
                return $object->display_name ?: $object->name;
            })
			->editColumn('created_by', function ($object) {
                return $object->user_create->name;
            })
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
            ->addColumn('action', function ($object) {
				$result = '<a href="' . route($this->route.'.edit',$object->id) .'" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				// $result .= '<a href="'. route($this->route.'.copy', $object->id) .'" title="Sao chép" class="btn btn-sm btn-info"><i class="fa fa-files-o"></i></a> ';
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;

            })
            ->addIndexColumn()
            ->make(true);
    }

	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function edit($id)
	{
		$object = ThisModel::findOrFail($id);
		$rolePermissions = $object->permissions->pluck('id');
		return view($this->view.'.edit', compact(['object', 'rolePermissions']));
	}

	public function copy($id)
	{
		$object = ThisModel::findOrFail($id);
		$rolePermissions = $object->permissions->pluck('id');
		return view($this->view.'.copy', compact(['object', 'rolePermissions']));
	}

	public function store(Request $request)
	{
		$rule = [
			'permissions' => 'required|array|min:1',
			'display_name' => 'required|unique:roles,name|unique:roles,display_name',
		];

		$validate = Validator::make(
			$request->all(),
			$rule
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
			$object->name = $request->display_name;
			$object->display_name = $request->display_name;
            $object->guard_name = 'admin';

			$object->save();

			$permissions = Permission::find($request->permissions);
			$object->syncPermissions($permissions);

			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			return Response::json($json);
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function update(Request $request, $id)
	{
		$rule = [
			'permissions' => 'required|array|min:1',
			'display_name' => 'required|unique:roles,name,'.$id
		];

		$validate = Validator::make(
			$request->all(),
			$rule
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
			// if ($request->status == 0 && !$object->canDelete()) {
			// 	$json->success = false;
			// 	$json->message = "Không thể khóa chức vụ này!";
			// 	return Response::json($json);
			// }
			$object->name = $object->display_name;
			if (empty($object->name)) $object->name = $request->display_name;

			$permissions = Permission::find($request->permissions);
			$object->syncPermissions($permissions);
			$object->save();

			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
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
				"message" => "Đã có người dùng nhận chức vụ này!",
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

	// // Xuất Excel
    // public function exportExcel() {
    //     return (new FastExcel(Role::all()))->download('roles.xlsx', function ($role) {
    //         return [
    //             'ID' => $role->id,
    //             'Tên chức vụ' => $role->display_name,
    //             'Trạng thái' => $role->status == 0 ? 'Khóa' : 'Hoạt động',
    //         ];
    //     });
    // }

    // // Xuất CSV
    // public function exportCSV() {
	// 	return (new FastExcel(Role::all()))->download('roles.csv', function ($role) {
    //         return [
    //             'ID' => $role->id,
    //             'Tên chức vụ' => $role->display_name,
    //             'Trạng thái' => $role->status == 0 ? 'Khóa' : 'Hoạt động',
    //         ];
    //     });
    // }

    // // Xuất PDF
    // public function exportPDF() {
    //     $roles = Role::all();
    //     $pdf = PDF::loadView($this->view.'.pdf', compact('roles'));
    //     return $pdf->download('roles.pdf');
    // }
}
