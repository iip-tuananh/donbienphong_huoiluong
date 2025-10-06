<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Admin\Project as ThisModel;
use App\Model\Admin\ProjectGallery;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use App\Model\Admin\ServiceType;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use DB;

class ProjectController extends Controller
{
    protected $view = 'admin.projects';
    protected $route = 'projects';

    public function index(Request $request)
    {
        return view($this->view.'.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
            ->editColumn('name', function ($object) {
                return '<a href = "'.route('projects.edit',$object->id).'" title = "Xem chi tiết">'.$object->name.'</a>';
            })
            ->editColumn('cate_id', function ($object) {
                return $object->category->name ?? '';
            })
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
            ->editColumn('updated_at', function ($object) {
                return Carbon::parse($object->updated_at)->format("d/m/Y H:i");
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_create->name ? $object->user_create->name : '';
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ? $object->user_update->name : '';
            })
            ->editColumn('image', function ($object) {
                return '<img style ="max-width:45px !important" src="' . ($object->image->path ?? '') . '"/>';
            })
            ->addColumn('action', function ($object) {
                $result = '<div class="btn-group btn-action">
                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class = "fa fa-cog"></i>
                </button>
                <div class="dropdown-menu">';
                $result = $result . ' <a href="'. route($this->route.'.edit', $object->id) .'" title="sửa" class="dropdown-item"><i class="fa fa-angle-right"></i>Sửa</a>';
                if ($object->canDelete()) {
                    $result = $result . ' <a href="' . route($this->route.'.delete', $object->id) . '" title="xóa" class="dropdown-item confirm"><i class="fa fa-angle-right"></i>Xóa</a>';
                }
                $result = $result . '</div></div>';
                return $result;
            })

            ->addIndexColumn()
            ->rawColumns(['name','action', 'image'])
            ->make(true);
    }

    public function create(Request $request)
    {
        return view($this->view.'.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:projects,name',
            'status' => 'required|in:0,1',
            'description' => 'nullable|max:255',
            'content' => 'nullable',
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:2000',
            'galleries' => 'required|array|min:1|max:20',
            'galleries.*.image' => 'required|file|mimes:png,jpg,jpeg,webp|max:10000',
        ];

        $validate = Validator::make(
            $request->all(),
            $rules
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
            $object->chu_dau_tu = $request->chu_dau_tu;
            $object->hang_muc = $request->hang_muc;
            $object->vi_tri = $request->vi_tri;
            $object->quy_mo = $request->quy_mo;
            $object->nam_hoan_thien = $request->nam_hoan_thien;
            $object->content = $request->content;
            $object->khu_vuc = $request->khu_vuc;
            $object->status = $request->status;
            $object->is_highlight = $request->is_highlight;

            $object->save();

            FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            $object->syncGalleries($request->galleries);

            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function edit(Request $request,$id)
    {
        $object = ThisModel::getDataForEdit($id);

        return view($this->view.'.edit', compact('object'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:projects,name,'.$id,
            'status' => 'required|in:0,1',
            'description' => 'nullable|max:255',
            'content' => 'nullable',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2000',
            'galleries' => 'nullable|array|min:1|max:20',
            'galleries.*.image' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:10000',
        ];

        $validate = Validator::make(
            $request->all(),
            $rules
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
            $object->chu_dau_tu = $request->chu_dau_tu;
            $object->hang_muc = $request->hang_muc;
            $object->vi_tri = $request->vi_tri;
            $object->quy_mo = $request->quy_mo;
            $object->nam_hoan_thien = $request->nam_hoan_thien;
            $object->content = $request->content;
            $object->khu_vuc = $request->khu_vuc;
            $object->status = $request->status;
            $object->is_highlight = $request->is_highlight;

            $object->save();

            if ($request->image) {
                if ($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }
            $object->syncGalleries($request->galleries);

            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw new Exception($e);
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
            if ($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
            }

            if($object->galleries) {
                foreach ($object->galleries as $gallery) {
                    FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, ProjectGallery::class);
                    $gallery->removeFromDB();
                }
            }

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
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }
}
