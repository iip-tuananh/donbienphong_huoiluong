<?php

namespace App\Http\Controllers\G7;

use Illuminate\Http\Request;
use App\Model\Common\User as ThisModel;
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
use App\Http\Traits\ResponseTrait;
use App\Helpers\FileHelper;

class G7UserController extends Controller
{
	use ResponseTrait;

	protected $view = 'g7.users';
	protected $route = 'G7User';

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
				return $object->user_update->name;
			})
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
			->editColumn('mobile', function ($object) {
                return $object->employee->mobile;
            })
			->editColumn('status', function ($object) {
                return getStatus($object->status, ThisModel::STATUSES);
            })
			->editColumn('created_by', function ($object) {
                return $object->user_create ? $object->user_create->name : '';
            })
            ->addColumn('action', function ($object) {
				$result = '';
				if ($object->canEdit()) {
					$result = '<a href="' . route($this->route.'.edit',$object->id) .'" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Khóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
				}
				return $result;

            })
			->rawColumns(['image', 'status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

	public function store(Request $request)
	{
		$rule = [
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|min:6|regex:/^[a-zA-Z0-9\@\$\!\%\*\#\?\&]+$/',
			'password_confirm' => 'required|same:password',
			'status' => 'required|in:0,1',
			'employee_id' => 'required|exists:g7_employees,id',
			'roles' => 'required|array|min:1',
			'roles.*' => 'required|exists:roles,id'
		];

		$validate = Validator::make(
			$request->all(),
			$rule,
			[]
		);

		if ($validate->fails()) {
			return $this->responseErrors("", $validate->errors());
		}

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->name = $request->name;
			$object->email = $request->email;
			$object->password = bcrypt($request->password);
			$object->type = 5;
			$object->status = $request->status;
			$object->g7_id = Auth::user()->g7_id;
			$object->employee_id = $request->employee_id;
			$object->save();

			$object->roles()->sync($request->roles);

			DB::commit();
			return $this->responseSuccess();
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function update(Request $request, $id)
	{
		$object = ThisModel::findOrFail($id);
		$rule = [
			'name' => 'nullable',
			'email' => 'nullable|email|unique:users,id,'.$id,
			'password' => 'nullable|min:6|regex:/^[a-zA-Z0-9\@\$\!\%\*\#\?\&]+$/',
			'password_confirm' => 'nullable|same:password',
			'status' => 'required|in:0,1',
			'employee_id' => 'required|exists:g7_employees,id',
			'roles' => 'required|array|min:1',
			'roles.*' => 'required|exists:roles,id'
		];

		$validate = Validator::make(
			$request->all(),
			$rule,
			[]
		);

		if ($validate->fails()) {
			return $this->responseErrors("", $validate->errors());
		}


		DB::beginTransaction();
		try {
			// $object->name = $request->name;
			// $object->email = $request->email;
			// if ($request->password != null) $object->password = bcrypt($request->password);
			// $object->type = 5;
			$object->status = $request->status;
			// $object->g7_id = Auth::user()->g7_id;
			$object->employee_id = $request->employee_id;
			$object->save();

			$object->roles()->sync($request->roles);

			DB::commit();
			return $this->responseSuccess();
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
				"message" => "Không thể khóa!",
				"alert-type" => "warning"
			);
		} else {
			$object->status = 0;
			$object->save();
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
