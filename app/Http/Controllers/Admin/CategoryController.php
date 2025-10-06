<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Category;
use Illuminate\Http\Request;
use App\Model\Admin\Category as ThisModel;
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

class CategoryController extends Controller
{
	protected $view = 'admin.categories';
	protected $route = 'Category';

	public function index()
	{
		$categories = ThisModel::getAll();
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
                DB::table('categories')->where('id',$row['id'])->update(['sort_order' => $i]);
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
				'parent_id' => 'nullable|exists:categories,id',
				'name' => 'required|max:255',
				'show_home_page' => 'required|in:0,1',
				'short_des' => 'nullable',
				'intro' => 'nullable',
				'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3000',
				'banner' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3000',
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
			$object->intro = $request->intro;
			$object->icon = $request->icon;
			$object->short_des = $request->short_des;
			$object->show_home_page = $request->show_home_page ?? 0;
			$object->link = $request->link;
			$object->save();

			// Cập nhật lại stt các danh mục có stt lớn hơn
			if($request->parent_id) {
				foreach(ThisModel::where('sort_order','>=',$stt)->where('id','<>', $object->id)->orderBy('sort_order','asc')->get() as $item) {
					$item->sort_order = $item->sort_order + 1;
					$item->save();
				}
			}

			if($request->image) {
				// FileHelper::uploadFile($request->image, 'categories', $object->id, ThisModel::class, 'image', 99);
				FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
			}

			if($request->banner) {
				// FileHelper::uploadFile($request->banner, 'category_banners', $object->id, ThisModel::class, 'banner', 99);
				FileHelper::uploadFileToCloudflare($request->banner, $object->id, ThisModel::class, 'banner');
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
				'short_des' => 'nullable',
                'show_home_page' => 'required|in:0,1',
                'intro' => 'nullable',
				'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3000',
				'banner' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:3000',
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
                if($object->parent_id == 0) {
                    $object->level = 0;
                    $object->sort_order = 0;
                } else {
                    $sort_order_max = Category::query()->orderBy('sort_order', 'desc')->first()->sort_order;
                    $object->parent_id = 0;
                    $object->level = 0;
                    $object->sort_order = $sort_order_max + 1;
                }
			}

			$object->name = $request->name;
			$object->intro = $request->intro;
			$object->short_des = $request->short_des;
            $object->icon = $request->icon;
			$object->show_home_page = $request->show_home_page ?? 0;
			$object->link = $request->link;
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
					FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
				}
				FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
			}

			if($request->banner) {
				if($object->banner) {
					FileHelper::deleteFileFromCloudflare($object->banner, $object->id, ThisModel::class, 'banner');
				}
				FileHelper::uploadFileToCloudflare($request->banner, $object->id, ThisModel::class, 'banner');
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
				"message" => "Không thể xóa!",
				"alert-type" => "warning"
			);
		} else {
			if($object->image) {
				FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
			}
			if($object->banner) {
				FileHelper::deleteFileFromCloudflare($object->banner, $object->id, ThisModel::class, 'banner');
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

    public function getParentCategory(Request $request)
    {
        $category = Category::query()->find($request->cate_id);

        $parent = $category->getParent();

        if ($parent) {
            return \Illuminate\Support\Facades\Response::json(['cate_parent_id' => $parent->id]);
        }

        return Response::json(['cate_parent_id' => null]);
    }

    public function addToHomePage(Request $request) {
        $rule = [
            'show_home_page' => 'required',
        ];

        $translate = [
            'show_home_page.required' => 'Bắt buộc phải nhập',
        ];

        $validate = Validator::make(
            $request->all(),
            $rule,
            $translate
        );

        $json = new stdClass();

        if ($validate->fails()) {
            $json->success = false;
            $json->errors = $validate->errors();
            $json->message = "Thao tác thất bại!";
            return Response::json($json);
        }

        $category = Category::query()->find($request->cate_id);

        $category->show_home_page = $request->show_home_page;
        $category->order_number = $request->order_number;

        $category->save();

        return Response::json(['success' => true]);
    }
}
