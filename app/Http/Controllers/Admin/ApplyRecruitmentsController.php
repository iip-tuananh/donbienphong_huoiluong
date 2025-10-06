<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\ResponseTrait;
use App\Model\Admin\ApplyRecruitment;
use App\Model\Admin\Category;
use App\Model\Admin\Contact;
use App\Model\Admin\Recruitment;
use Illuminate\Http\Request;
use App\Model\Admin\ApplyRecruitment as ThisModel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
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

class ApplyRecruitmentsController extends Controller
{
    use ResponseTrait;
    protected $view = 'admin.apply_recruitments';
    protected $route = 'apply-recruitments';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
            ->editColumn('created_at', function ($object) {
                return formatDate($object->updated_at);
            })
            ->editColumn('recruitment_id', function ($object) {
                $recruitment = Recruitment::query()->find($object->recruitment_id);
                if($recruitment) return $recruitment->title;
                return '';
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="'.route('apply-recruitments.delete', $object->id).'" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                $result .= '&nbsp;<a href="javascript:void(0)" title="Chi tiết" class="btn btn-sm btn-primary show-detail"><i class="fas fa-eye"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function getDetail(Request $request, $id) {
        $applyRecruitment = ApplyRecruitment::query()->find($id);
        if($applyRecruitment) {
            $applyRecruitment->send_at = \Illuminate\Support\Carbon::parse($applyRecruitment->created_at)->format('d/m/Y H:i');
            $recruitment = Recruitment::query()->find($applyRecruitment->recruitment_id);
            $applyRecruitment->job_apply = $recruitment->title ?? '';
            $applyRecruitment->file = null;
            if($applyRecruitment->attachments) {
                $array = explode(',', $applyRecruitment->attachments);
                $array = array_map('trim', $array);
                $array = array_filter($array);
                $applyRecruitment->file = $array[1];
            }

            return $this->responseSuccess("", $applyRecruitment);
        } else {
            return $this->responseErrors();
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



}
