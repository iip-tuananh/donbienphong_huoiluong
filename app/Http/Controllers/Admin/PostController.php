<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Block;
use App\Model\Admin\BlockGallery;
use App\Model\Admin\Post;
use App\Model\Admin\PostCategorySpecial;
use App\Model\Admin\PostFile;
use App\Model\Admin\Tagable;
use Illuminate\Http\Request;
use App\Model\Admin\Post as ThisModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

class PostController extends Controller
{
	protected $view = 'admin.posts';
	protected $route = 'Post';

	public function index()
	{
		return view($this->view.'.index');
	}

	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);
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
                // Lấy danh sách tên
                $names = collect($object->category_specials ?? [])
                    ->pluck('name')
                    ->filter()
                    ->values();

                if ($names->isEmpty()) {
                    return '<span class="badge badge-light text-muted">Không có</span>';
                }

                // Bảng màu cố định theo tên (ổn định theo hash)
                $palette = ['primary','info','success','warning','danger','secondary'];
                $makeBadge = function ($name) use ($palette) {
                    $lower = function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
                    $idx   = hexdec(substr(hash('crc32b', $lower), 0, 2)) % count($palette);
                    $class = 'badge-'.$palette[$idx];
                    return '<span class="badge '.$class.' mr-1 mb-1"><i class="fas fa-tag mr-1"></i>'.e($name).'</span>';
                };

                $max = 3; // số badge hiển thị
                $html = $names->take($max)->map($makeBadge)->implode('');

                if ($names->count() > $max) {
                    $more  = $names->slice($max);
                    $title = e($more->implode(', '));
                    $html .= '<span class="badge badge-light text-primary mr-1 mb-1" title="'.$title.'">+'.($names->count()-$max).'</span>';
                }

                // data-order để sắp xếp theo chuỗi tên gộp
                return '<div class="d-flex flex-wrap" data-order="'.e($names->implode(', ')).'">'.$html.'</div>';
            })
            ->addColumn('tags', function ($post) {
                if ($post->tags->isEmpty()) {
                    return '<span class="text-muted">—</span>';
                }

                $limit = 3;
                $chips = $post->tags->take($limit)->map(function ($tag) {
                    $name = e($tag->name);
                    return '<span class="tag-badge">'.$name.'</span>';
                })->implode(' ');

                $extra = $post->tags->count() - $limit;
                if ($extra > 0) {
                    $moreNames = e($post->tags->slice($limit)->pluck('name')->join(', '));
                    $chips .= ' <span class="tag-badge more" title="'.$moreNames.'">+'. $extra .'</span>';
                }

                return $chips;
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
                $result = $result . ' <a href="" title="thêm vào danh mục đặc biệt" class="dropdown-item add-category-special"><i class="fa fa-angle-right"></i>Thêm vào danh mục đặc biệt</a>';

                $result = $result . '</div></div>';
                return $result;
			})

			->addIndexColumn()
			->rawColumns(['name','action', 'image', 'category_special', 'tags'])
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
                'name' => 'required',
                'type' => 'required',
                'price' => 'nullable|required_if:type,2|numeric|min:1',
                'cate_id' => 'required',
				'status' => 'required|in:0,1',
				'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10000'
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
			$object->type = $request->type;
			$object->price = $request->price;
			$object->name = $request->name;
			$object->body = $request->body;
			$object->intro = $request->intro;
			$object->status = $request->status;
			$object->is_hot = $request->is_hot;


            $videos = $request->input('videos', []);
            $filtered = collect($videos)
                ->filter(function ($item) {
                    return isset($item['link']) && trim($item['link']) !== '';
                })
                ->values()
                ->all();

            $object->videos = $filtered;

			$object->save();

            $object->tags()->sync($request->tag_ids ?? []);

			FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');

            $object->syncFiles($request);

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
				'name' => 'required|unique:posts,name,'.$id,
				'cate_id' => 'required',
                'type' => 'required',
                'price' => 'nullable|required_if:type,2|numeric|min:1',
                'status' => 'required|in:0,1',
				'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10000',
                'files.*' => 'file|mimes:pdf,doc,docx|max:20480'
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
            $object->type = $request->type;
            $object->price = $request->price;
            $object->name = $request->name;
            $object->body = $request->body;
            $object->intro = $request->intro;
            $object->status = $request->status;
            $object->is_hot = $request->is_hot;


            $videos = $request->input('videos', []);
            $filtered = collect($videos)
                ->filter(function ($item) {
                    return isset($item['link']) && trim($item['link']) !== '';
                })
                ->values()
                ->all();

            $object->videos = $filtered;

            $object->save();

            if ($request->image) {
                if($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }

            $object->tags()->sync($request->tag_ids ?? []);

            $object->syncFiles($request);

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

            PostCategorySpecial::query()->where('post_id', $id)->delete();
            Tagable::query()->where('tagable_id', $id)
                ->where('tagable_type', ThisModel::class)
                ->delete();

            $dir = public_path('uploads/files');
            $object->files()->get()->each(function($pf) use ($dir){
                $path = $dir . DIRECTORY_SEPARATOR . $pf->file_name;
                if (is_file($path)) { @unlink($path); }
                $pf->delete();
            });

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
