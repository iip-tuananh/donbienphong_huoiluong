<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Block;
use App\Model\Admin\BlockGallery;
use App\Model\Admin\Post;
use App\Model\Admin\Spa;
use Illuminate\Http\Request;
use App\Model\Admin\Spa as ThisModel;
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

class SpaController extends Controller
{
    protected $view = 'admin.spa';
    protected $route = 'Spa';

    public function index()
    {
        return view($this->view.'.index');
    }


    public function edit(Request $request)
    {
        $object = ThisModel::getDataForEdit(1);

        return view($this->view.'.edit', compact('object'));
    }

    public function show(Request $request,$id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view.'.show', compact('object'));
    }

    public function update(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'title_eng' => 'required',
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
            $object = ThisModel::query()->find(1);

            if(! $object) $object = new Spa();

            $object->title = $request->title;
            $object->title_eng = $request->title_eng;
            $object->intro = $request->intro;
            $object->intro_eng = $request->intro_eng;
            $object->body = $request->body;
            $object->body_eng = $request->body_eng;
            $object->save();

            if ($request->image) {
                if($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }

            // blocks
            foreach ($request->blocks as $block) {
                $objBlock = Block::query()->where('code', $block['code'])
                    ->where('blockable_id', $object->id)
                    ->where('blockable_type', Spa::class)
                    ->first();


                if(! $objBlock) $objBlock = new Block();
                $objBlock->title = $block['title'] ?? null;
                $objBlock->title_eng = $block['title_eng'] ?? null;
                $objBlock->body = $block['body'] ?? null;
                $objBlock->body_eng = $block['body_eng'] ?? null;
                $objBlock->code = $block['code'];
                $objBlock->blockable_id = $object->id;
                $objBlock->blockable_type = Spa::class;
                $objBlock->save();

                if(@$block['galleries']) {
                    $objBlock->syncGalleries($block['galleries']);
                }

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
