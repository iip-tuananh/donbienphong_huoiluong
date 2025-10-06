<?php

namespace App\Http\Controllers\Admin;

use App\Http\Traits\ResponseTrait;
use App\Model\Admin\Category;
use App\Model\Admin\Contact;
use Illuminate\Http\Request;
use App\Model\Admin\Contact as ThisModel;
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

class ContactController extends Controller
{
    use ResponseTrait;
    protected $view = 'admin.contacts';
    protected $route = 'contacts';

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
                return Carbon::parse($object->created_at)->format('d-m-Y H:i');
            })
            ->editColumn('content', function ($object) {
                return Str::limit($object->content, 100);
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="javascript:void(0)" title="Chi tiết" class="btn btn-sm btn-primary show-detail"><i class="fas fa-eye"></i></a>';
                $result .= '&nbsp;<a href="'.route('contacts.delete', $object->id).'" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                return $result;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function getContactDetail(Request $request, $id) {
        $contact = Contact::query()->find($id);

        if($contact) {
            $contact->day_send = \Illuminate\Support\Carbon::parse($contact->created_at)->format('d/m/Y H:i');
            $contact->cate = $contact->cate_id ? Category::query()->find($contact->cate_id)->name : '';
            return $this->responseSuccess("", $contact);
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
