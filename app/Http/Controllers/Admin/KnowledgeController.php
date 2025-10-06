<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Block;
use App\Model\Admin\BlockGallery;
use App\Model\Admin\Post;
use Illuminate\Http\Request;
use App\Model\Admin\Post as ThisModel;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use DB;

class KnowledgeController extends Controller
{
    protected $view = 'admin.knowledge';
    protected $route = 'knowledge';

    public function index()
    {
        return view($this->view.'.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request, 'knowledge');
        return Datatables::of($objects)
            ->editColumn('name', function ($object) {
                return '<a href = "'.route('Post.show',$object->id).'" title = "Xem chi tiết">'.$object->name.'</a>';
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
            ->editColumn('image', function ($object) {
                return '<img style ="max-width:45px !important" src="' . ($object->image->path ?? '') . '"/>';
            })
            ->editColumn('cate_id', function ($object) {
                return $object->category->name ?? '';
            })
            ->addColumn('category_special', function ($object) {
                return $object->category_specials->implode('name', ', ');
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

    public function create()
    {
        return view($this->view.'.create');
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'cate_id' => 'required|exists:post_categories,id',
                'name' => 'required',
                'status' => 'required|in:0,1',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:10000'
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
            $object->cate_id = $request->cate_id;
            $object->name = $request->name;
            $object->body = $request->body;
            $object->intro = $request->intro;
            $object->status = $request->status;
            $object->type = 'knowledge';
            $object->save();

            FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');

            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            return Response::json($json);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw new Exception($e->getMessage());
        }
    }

    public function edit(Request $request,$id)
    {
        $object = ThisModel::getDataForEdit($id);

        return view($this->view.'.edit', compact('object'));
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
                'cate_id' => [
                    'required',
                ],
                'name' => 'required|unique:posts,name,'.$id,
                'status' => 'required|in:0,1',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10000',
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


            $object->cate_id = $request->cate_id;
            $object->name = $request->name;
            $object->body = $request->body;
            $object->intro = $request->intro;
            $object->status = $request->status;
            $object->type = 'knowledge';

            $object->save();

            if ($request->image) {
                if($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }

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
            if($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
            }
            $object->delete();
            $message = array(
                "message" => "Thao tác thành công!",
                "alert-type" => "success"
            );
        }


        return redirect()->route($this->route.'.index')->with($message);
    }

    // Xuất Excel
    public function exportExcel() {
        return (new FastExcel(ThisModel::all()))->download('danh_sach_vat_tu.xlsx', function ($object) {
            return [
                'ID' => $object->id,
                'Tên' => $object->name,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    public function getData(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }

    public function addToCategorySpecial(Request $request) {
        $post = Post::query()->find($request->post_id);

        $post->category_specials()->sync($request->category_special_ids);

        return Response::json(['success' => true, 'message' => 'Thêm bài viết vào danh mục đặc biệt thành công']);
    }
}
