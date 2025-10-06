<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Post;
use App\Model\Admin\PostCategory;
use Illuminate\Http\Request;
use App\Model\Admin\PostCategory as ThisModel;
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
use Auth;

class BlogCategoryController extends Controller
{
    protected $view = 'admin.blog_categories';
    protected $route = 'blogCategory';

    public function index()
    {
        $categories = ThisModel::query()->where('type', PostCategory::TYPE_BLOG)->get();
        return view($this->view.'.index', compact('categories'));
    }


    public function nestedSort(Request $request)
    {
        if ($request->ajax()) {
            $json = new stdClass();
            $data = $request->get('dataString');
            $data = json_decode($data['data']);
            $readbleArray = $this->parseJsonArray($data);
            $i=0;
            foreach($readbleArray as $row){
                if($row['parentID']) {
                    $level = ThisModel::where('id', $row['parentID'])->first()->level + 1;
                } else {
                    $level = 0;
                }
                $i++;
                DB::table('post_categories')->where('id',$row['id'])->update(['sort_order' => $i]);
            }
            $json->success = true;
            $json->message = "Sắp xếp thành công";
            return Response::json($json);
        }
    }

    public function parseJsonArray($jsonArray, $parentID = 0)
    {
        $return = array();
        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = array();
            if (isset($subArray->children)) {
                $returnSubSubArray = $this->parseJsonArray($subArray->children, $subArray->id);
            }
            $return[] = array('id' => $subArray->id, 'parentID' => $parentID);
            $return = array_merge($return, $returnSubSubArray);
        }
        return $return;
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('name', function ($object) {
                return $object->name;
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_create->name ? $object->user_create->name : '';
            })
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ? $object->user_update->name : '';
            })

            ->addColumn('action', function ($object) {
                $result = '';
                if($object->canEdit()) {
                    $result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                }
                if ($object->canDelete()) {
                    $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a>';
                }
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['name','action'])
            ->make(true);
    }

    public function create()
    {
        $categories = ThisModel::getForSelect();
        // dd($categories);
        return view($this->view.'.create',compact('categories'));
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'parent_id' => 'nullable',
                'name' => 'required|max:255',
                'name_eng' => 'required|max:255',
                'intro' => 'nullable',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:3000'

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

            if($request->parent_id) {
                $parent = ThisModel::where('id',$request->parent_id)->first();
                if($parent->level + 1 > 3) {
                    $json->success = false;
                    $json->message = "Menu không được quá 3 cấp cha con!";
                    return Response::json($json);
                }
                $stt = ThisModel::where('parent_id', $request->parent_id)->count();
                if($stt > 0) {
                    $stt += $stt;
                } else {
                    $stt = $parent->sort_order + 1;
                }
                $object->parent_id = $request->parent_id;
                $object->level = $parent->level + 1;
                $object->sort_order = $stt;
            } else {
                $object->level = 0;
                $object->sort_order = 0;
            }
            $object->name = $request->name;
            $object->name_eng = $request->name;
            $object->type = PostCategory::TYPE_BLOG;
            $object->intro = $request->intro;
            $object->intro_eng = $request->intro_eng;

            $object->save();

            // Cập nhật lại stt các danh mục có stt lớn hơn
            if($request->parent_id) {
                foreach(ThisModel::where('sort_order','>=',$stt)->where('id','<>', $object->id)->orderBy('sort_order','asc')->get() as $item) {
                    $item->sort_order = $item->sort_order + 1;
                    $item->save();
                }
            }

            if($request->image) {
                // FileHelper::uploadFile($request->image, 'post_categories', $object->id, ThisModel::class, 'image',1);
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }
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

    public function show(Request $request,$id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view.'.show', compact('object'));
    }

    public function edit($id)
    {
        $object = ThisModel::getDataForEdit($id);
        $categories = ThisModel::getAllForEdit($id);
        return view($this->view.'.edit', compact('object','categories'));
    }

    public function update(Request $request, $id)
    {

        $validate = Validator::make(
            $request->all(),
            [
                'parent_id' => 'nullable',
                'name' => 'required|max:255',
                'name_eng' => 'required|max:255',
                'intro' => 'nullable',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000'

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
            $object = ThisModel::find($id);

            if($request->parent_id) {
                $parent = ThisModel::where('id',$request->parent_id)->first();
                if($parent->level + 1 > 3) {
                    $json->success = false;
                    $json->message = "Menu không được quá 3 cấp cha con!";
                    return Response::json($json);
                }
                $stt = ThisModel::where('parent_id', $request->parent_id)->count();
                if($stt > 0) {
                    $stt += $stt;
                } else {
                    $stt = $parent->sort_order + 1;
                }
                $object->parent_id = $request->parent_id;
                $object->level = $parent->level + 1;
                $object->sort_order = $stt;
            } else {
                $object->level = 0;
                $object->sort_order = 0;
            }
            $object->name = $request->name;
            $object->name_eng = $request->name_eng;
            $object->intro = $request->intro;
            $object->intro_eng = $request->intro_eng;
            $object->type = PostCategory::TYPE_BLOG;

            $object->save();

            // Cập nhật lại stt các danh mục có stt lớn hơn
            if($request->parent_id) {
                foreach(ThisModel::where('sort_order','>=',$stt)->where('id','<>', $object->id)->orderBy('sort_order','asc')->get() as $item) {
                    $item->sort_order = $item->sort_order + 1;
                    $item->save();
                }
            }

            if($request->image) {
                if($object->image) {
                    // FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                // FileHelper::uploadFile($request->image, 'post_categories', $object->id, ThisModel::class, 'image',1);
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }


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
                "message" => "Không thể xóa. Đã có bài viết đã sử dụng danh mục này!",
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


    public function getDataForEdit(Request $request, $id) {
        $json = new stdclass();
        $json->success = true;
        $json->data = ThisModel::getDataForEdit($id);
        return Response::json($json);
    }

    // Xuất Excel
    public function exportExcel(Request $request)
    {
        return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_sach_lich_hen.xlsx', function ($object) {
            return [
                'Khách hàng' => $object->customer->name,
                'SĐT khách' => $object->customer->mobile,
                'Giờ hẹn' => \Carbon\Carbon::parse($object->booking_time)->format('H:m d/m/Y'),
                'Ghi chú' => $object->note,
                'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
            ];
        });
    }

    // Xuất PDF
    public function exportPDF(Request $request) {
        $data = ThisModel::searchByFilter($request);
        $pdf = PDF::loadView($this->view.'.pdf', compact('data'));
        return $pdf->download('danh_sach_lich_hen.pdf');
    }

    public function addToHomePage(Request $request) {
        $category = PostCategory::query()->find($request->cate_id);

        $category->show_home_page = $request->show_home_page;
        $category->order_number = $request->order_number;

        $category->save();

        return Response::json(['success' => true]);
    }
}
