<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Document\DocumentStoreRequest;
use App\Http\Requests\Document\DocumentUpdateRequest;
use App\Http\Requests\Experience\ExperienceStoreRequest;
use App\Http\Requests\Experience\ExperienceUpdateRequest;
use App\Http\Requests\Rooms\RoomStoreRequest;
use App\Http\Requests\Rooms\RoomUpdateRequest;
use App\Model\Admin\Document;
use App\Model\Admin\Experience;
use App\Model\Admin\Room;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use App\Helpers\FileHelper;
use App\Model\Common\User;
use App\Model\Common\ActivityLog;
use Auth;

class DocumentController extends Controller
{
    private $view = 'admin.document';
    private $route = 'document';

    public function index()
    {
        return view('admin.document.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = Document::searchByFilter($request);
        return Datatables::of($objects)
            ->addColumn('name', function ($object) {
                return $object->name;
            })
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_create->name ? $object->user_create->name : '';
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ? $object->user_update->name : '';
            })
            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';

                if($object->canEdit()) {
                    $result = $result . ' <a href="'. route($this->route.'.edit', $object->id) .'" title="sửa" class="dropdown-item"><i class="fa fa-angle-right"></i>Sửa</a>';
                }
                if ($object->canDelete()) {
                    $result = $result . ' <a href="' . route($this->route.'.delete', $object->id) . '" title="xóa" class="dropdown-item confirm"><i class="fa fa-angle-right"></i>Xóa</a>';

                }

                $result = $result . '</div></div>';
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

    public function store(DocumentStoreRequest $request)
    {
        $json = new stdClass();
        DB::beginTransaction();
        try {
            $object = new Document();
            $object->title = $request->title;
            $object->title_eng = $request->title_eng;


            $object->save();

            $object->syncGalleries($request->galleries);
            $object->syncVideo($request->videos);


            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function edit($id)
    {
        $object = Document::getDataForEdit($id);

        return view($this->view.'.edit', compact('object'));
    }

    public function update(DocumentUpdateRequest $request, $id)
    {
        $json = new stdClass();

        DB::beginTransaction();
        try {
            $object = Document::findOrFail($id);

            if (!$object->canEdit()) {
                $json->success = false;
                $json->message = "Bạn không có quyền sửa hàng hóa này";
                return Response::json($json);
            }

            $object->title = $request->title;
            $object->title_eng = $request->title_eng;

            $object->save();

            $object->syncGalleries($request->galleries);
            $object->syncVideo($request->videos);


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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $object = Document::findOrFail($id);
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
        return redirect()->route($this->route.'.index')->with($message);
    }


    public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = Document::getDataForEdit($id);
        return Response::json($json);
    }

}
