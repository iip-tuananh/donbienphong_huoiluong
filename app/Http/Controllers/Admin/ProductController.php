<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Products\ProductStoreRequest;
use App\Http\Requests\Products\ProductUpdateRequest;
use App\Model\Admin\AttributeValue;
use App\Model\Admin\Manufacturer;
use App\Model\Admin\Post;
use App\Model\Admin\Product;
use App\Model\Admin\ProductCategorySpecial;
use App\Model\Admin\ProductGallery;
use App\Model\Admin\ProductVideo;
use App\Model\Admin\Tag;
use Cassandra\Exception\ProtocolException;
use Illuminate\Http\Request;
use App\Model\Admin\Product as ThisModel;
use App\Model\Common\Unit;
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

class ProductController extends Controller
{
	protected $view = 'admin.products';
	protected $route = 'Product';

	public function index()
	{
		return view($this->view.'.index');
	}

	// Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
			->addColumn('name', function ($object) {
				return $object->name;
			})
			->editColumn('base_price', function ($object) {
				return formatCurrent($object->base_price);
			})
			->editColumn('price', function ($object) {
				return $object->price > 0 ? formatCurrent($object->price) : 'Liên hệ';
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
			->editColumn('cate_id', function ($object) {
					return $object->category ? $object->category->name : '';
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

                $result = $result . ' <a href="" title="thêm vào danh mục đặc biệt" class="dropdown-item add-category-special"><i class="fa fa-angle-right"></i>Thêm vào danh mục đặc biệt</a>';
                $result = $result . '</div></div>';
                return $result;
			})
			->addIndexColumn()
			->rawColumns(['action', 'image', 'category_special'])
			->make(true);
    }

	public function create()
	{
        $tags = Tag::query()->where('type', Tag::TYPE_PRODUCT)->latest()->get();

		return view($this->view.'.create', compact('tags'));
	}

	public function store(ProductStoreRequest $request)
	{
		$json = new stdClass();
		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->name = $request->name;
			$object->code = $request->code;
			$object->cate_id = $request->cate_id;
			$object->intro = $request->intro;
			$object->short_des = $request->short_des;
			$object->body = $request->body;
			$object->base_price = $request->base_price;
			$object->price = $request->price;
			$object->status = $request->status;
			$object->manufacturer_id = $request->manufacturer_id;
			$object->origin_id = $request->origin_id;
            $object->url_custom = $request->url_custom;
            $object->state = $request->state ?? Product::CON_HANG;
            $object->is_pin = $request->is_pin ?? Product::NOT_PIN;

			$object->save();

            FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            FileHelper::uploadFileToCloudflare($request->image_back, $object->id, ThisModel::class, 'image_back');
			$object->syncGalleries($request->galleries);

            if($request->input('attributes')) {
                $object->syncAttributes($request->input('attributes'));
            }

            if(isset($request->all()['videos'])) {
                foreach ($request->all()['videos'] as $video) {
                    ProductVideo::query()->create([
                        'link' => $video['link'],
                        'video' => $video['video'],
                        'product_id' => $object->id,
                    ]);
                }
            }

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
		$object = ThisModel::getDataForEdit($id);
        $tags = Tag::query()->where('type', Tag::TYPE_PRODUCT)->latest()->get();
        $object->tag_ids = $object->tags->pluck('id')->toArray();

        return view($this->view.'.edit', compact('object','tags'));
	}

	public function update(ProductUpdateRequest $request, $id)
	{
		$json = new stdClass();

		DB::beginTransaction();
		try {
			$object = ThisModel::findOrFail($id);

			if (!$object->canEdit()) {
				$json->success = false;
				$json->message = "Bạn không có quyền sửa hàng hóa này";
				return Response::json($json);
			}

            $object->code = $request->code;
			$object->name = $request->name;
			$object->cate_id = $request->cate_id;
			$object->intro = $request->intro;
			$object->short_des = $request->short_des;
			$object->body = $request->body;
			$object->base_price = $request->base_price;
			$object->price = $request->price;
			$object->status = $request->status;
			$object->manufacturer_id = $request->manufacturer_id;
			$object->origin_id = $request->origin_id;
            $object->url_custom = $request->url_custom;
            $object->state = $request->state ?? Product::CON_HANG;
            $object->is_pin = $request->is_pin ?? Product::NOT_PIN;

			$object->save();

			if($request->image) {
				if($object->image) {
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
				}
				FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
			}
			if($request->image_back) {
				if($object->image_back) {
					FileHelper::deleteFileFromCloudflare($object->image_back, $object->id, ThisModel::class, 'image_back');
				}
				FileHelper::uploadFileToCloudflare($request->image_back, $object->id, ThisModel::class, 'image_back');
			}

			$object->syncGalleries($request->galleries);

            if($request->input('attributes')) {
                $object->syncAttributes($request->input('attributes'));
            }

            if(isset($request->all()['videos'])) {
                ProductVideo::query()->where('product_id', $object->id)->delete();
                foreach ($request->all()['videos'] as $video) {
                    ProductVideo::query()->create([
                        'link' =>$video['link'],
                        'video' => $video['video'],
                        'product_id' => $object->id,
                    ]);
                }
            }

			DB::commit();
			ActivityLog::createRecord("Cập nhật hàng hóa thành công", route('Product.edit', $object->id, false));
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

		$object = ThisModel::findOrFail($id);
		if (!$object->canDelete()) {
			$message = array(
				"message" => "Không thể xóa!",
				"alert-type" => "warning"
			);
		} else {
            if($object->galleries->count() > 0) {
                foreach ($object->galleries as $gallery) {
                    if ($gallery->image) {
                        FileHelper::deleteFileFromCloudflare($gallery->image, $gallery->id, ProductGallery::class);
                    }
                    $gallery->removeFromDB();
                }
            }
            if($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
            }
            if($object->image_back) {
                FileHelper::deleteFileFromCloudflare($object->image_back, $object->id, ThisModel::class, 'image_back');
            }

            ProductCategorySpecial::query()->where('product_id', $object->id)->delete();

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

	// Xuất Excel
	public function exportExcel(Request $request)
	{
		return (new FastExcel(ThisModel::searchByFilter($request)))->download('danh_sach_hang_hoa.xlsx', function ($object) {
			if(Auth::user()->type == User::G7 || Auth::user()->type == User::NHOM_G7) {
				return [
					'ID' => $object->id,
					'Mã' => $object->code,
					'Tên' => $object->name,
					'Loại' => $object->category->name,
					'Giá đề xuất' => formatCurrency($object->price),
					'Giá bán' => formatCurrency($object->g7_price->price),
					'Điểm tích lũy' => $object->point,
					'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
				];
			} else {
				return [
					'ID' => $object->id,
					'Mã' => $object->code,
					'Tên' => $object->name,
					'Loại' => $object->category->name,
					'Giá đề xuất' => formatCurrency($object->price),
					'Điểm tích lũy' => $object->point,
					'Trạng thái' => $object->status == 0 ? 'Khóa' : 'Hoạt động',
				];
			}
		});
	}

	// Xuất PDF
	public function exportPDF(Request $request) {
		$data = ThisModel::searchByFilter($request);
		$pdf = PDF::loadView($this->view.'.pdf', compact('data'));
		return $pdf->download('danh_sach_hang_hoa.pdf');
	}

    public function addToCategorySpecial(Request $request) {
        $product = Product::query()->find($request->product_id);

        $product->category_specials()->sync($request->category_special_ids);

        return Response::json(['success' => true, 'message' => 'Thao tác thành công']);
    }

    // xóa nhiều sản phẩm
    public function actDelete(Request $request) {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $product_ids = explode(',', $request->product_ids);
        foreach ($product_ids as $product_id) {
            $product = ThisModel::findOrFail($product_id);
            if($product->image) {
                FileHelper::deleteFileFromCloudflare($product->image, $product->id, ThisModel::class, 'image');
            }
        }

        Product::query()->whereIn('id', $product_ids)->delete();

        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );

        return redirect()->route($this->route.'.index')->with($message);
    }

    public function deleteFile(Request $request, $id) {
        $json = new \stdClass();
        $req = Product::findOrFail($id);

        $attachments = explode(", ", $req->attachments);

        if (!$request->file || !in_array($request->file, $attachments)) {
            $json->success = false;
            $json->message = "Không có file";
            return \Response::json($json);
        }

        if (file_exists(public_path().$request->file)) unlink(public_path().$request->file);

        $attachments = array_diff($attachments, [$request->file]);
        $req->attachments = join(", ", $attachments);
        $req->save();
        $json->success = true;
        $json->message = "Xóa thành công";
        $json->data = $req;

        return \Response::json($json);
    }

}
