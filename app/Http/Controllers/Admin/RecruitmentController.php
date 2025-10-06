<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Recruitments\RecruitmentStoreRequest;
use App\Model\Admin\Partner;
use App\Model\Admin\Project;
use App\Model\Admin\Recruitment;
use Illuminate\Http\Request;
use App\Model\Admin\Recruitment as ThisModel;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Validator;
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
use App\Model\Common\User;

class RecruitmentController extends Controller
{
    use ResponseTrait;

    protected $view = 'admin.recruitments';
    protected $route = 'recruitments';

    public function index()
    {
        return view($this->view.'.index');
    }

    // Hàm phân trang, search cho datatable
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
            ->editColumn('expiration_date', function ($object) {
                return Carbon::parse($object->expiration_date)->format("d/m/Y");
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ?? '';
            })
            ->addColumn('action', function ($object) {
                $result = '<a href="' . route($this->route.'.edit',$object->id) .'" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Khóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';

                return $result;
            })
            ->addColumn('title', function ($object) {
                return $object->title;
            })
            ->addColumn('address', function ($object) {
                return $object->address;
            })
            ->rawColumns(['action'])
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

    public function store(RecruitmentStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $recruitment = new Recruitment();
            $recruitment->title = $request->title;
            $recruitment->address = $request->address;
            $recruitment->expiration_date = $request->expiration_date;
            $recruitment->salary = $request->salary;
            $recruitment->description = $request->description;
            $recruitment->save();


            if($request->image) {
                FileHelper::uploadFile($request->image, 'recruitments', $recruitment->id, ThisModel::class, 'image', 99);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(RecruitmentStoreRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $recruitment = ThisModel::findOrFail($id);

            $recruitment->title = $request->title;
            $recruitment->address = $request->address;
            $recruitment->expiration_date = $request->expiration_date;
            $recruitment->salary = $request->salary;
            $recruitment->description = $request->description;
            $recruitment->save();

            if($request->image) {
                if($recruitment->image) FileHelper::forceDeleteFiles($recruitment->image->id, $recruitment->id, ThisModel::class, 'image');
                FileHelper::uploadFile($request->image, 'recruitments', $recruitment->id, ThisModel::class, 'image', 99);
            }

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete($id)
    {
        $object = Recruitment::query()->find($id);
        $object->delete();
        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );
        return redirect()->route($this->route.'.index')->with($message);
    }



    // Xuất Excel
    public function exportExcel() {
        return (new FastExcel(ThisModel::all()))->download('danh_sach_tai_khoan.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên' => $object->name,
                'email' => $object->email,
                'Loại' => $object->getTypeUser($object->type),
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    // Xuất PDF
    public function exportPDF() {
        $data = ThisModel::all();
        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_tai_khoan.pdf');
    }
}
