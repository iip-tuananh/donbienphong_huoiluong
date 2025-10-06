<?php

namespace App\Http\Controllers\G7;

use App\Model\G7\G7ProductPrice;
use Illuminate\Http\Request;
use App\Model\G7\G7Product as ThisModel;
use App\Model\Common\Unit;
use App\Model\Product;
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
use Illuminate\Validation\Rule;
use Auth;

class G7ProductController extends Controller
{
	protected $view = 'g7.products';
	protected $route = 'G7Product';

	public function index()
	{
		return view($this->view.'.index');
	}

    public function searchData(Request $request)
    {
		$objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
			->editColumn('created_at', function ($object) {
				return Carbon::parse($object->created_at)->format("d/m/Y");
			})
			->editColumn('created_by', function ($object) {
				return $object->user_create->name ? $object->user_create->name : '';
			})
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name ? $object->user_update->name : '';
			})
			->editColumn('category', function ($object) {
				return $object->category ? $object->category->name : '';
			})
			->editColumn('status', function ($object) {
				return getStatus($object->status, ThisModel::STATUSES);
			})
			->addColumn('action', function ($object) {
				$result = '<a href="' . route($this->route.'.edit', $object->id) . '" title="Sửa" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a> ';
				if ($object->canDelete()) {
					$result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a> ';
				}
				return $result;
			})
			->rawColumns(['status', 'action'])
			->addIndexColumn()
			->make(true);
    }

	public function create()
	{
		return view($this->view.'.create');
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		return view($this->view.'.edit', compact(['object']));
	}

	public function store(Request $request)
	{
		$rule = [
			'root_product_id' => 'nullable|exists:products,id',
			'price' => 'required|integer',
			'points' =>'nullable|integer',
		];

		if (!$request->root_product_id) {
			$rule = array_merge($rule, [
				'name' => [
					'required',
					Rule::unique('g7_products')->where(function ($query) use ($request) {
						return $query->where('g7_id', Auth::user()->g7_id);
					}),
				],
				'code' => [
					'required',
					Rule::unique('g7_products')->where(function ($query) use ($request) {
						return $query->where('g7_id', Auth::user()->g7_id);
					}),
					'unique:products'
				],
				'product_category_id' => 'required|exists:product_categories,id',
				'unit_id' => 'required|exists:units,id',
				'image' => 'required|file|mimes:jpg,jpeg,png|max:3000'
			]);
		} else {
			$rule = array_merge($rule, [
				'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000'
			]);
		}

		$validate = Validator::make(
			$request->all(),
			$rule
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
			if ($request->root_product_id) {
				$exist = ThisModel::where('root_product_id', $request->root_product_id)->where('g7_id', Auth::user()->g7_id)->first();

				if ($exist) {
					$json->success = false;
					$json->message = "Đã tồn tại trên hệ thống!";
					return Response::json($json);
				}

				$root_product = Product::find($request->root_product_id);
				$object->root_product_id = $request->root_product_id;
				$object->name = $root_product->name;
				$object->code = $root_product->code;
				$object->product_category_id = $root_product->product_category_id;
				$object->unit_id = $root_product->unit_id;
				$object->unit_name = $root_product->unit_name;
			} else {
				$object->name = $request->name;
				$object->code = $request->code;
				$object->product_category_id = $request->product_category_id;
				$object->unit_id = $request->unit_id;
				$object->unit_name = Unit::find($request->unit_id)->name;
			}

			$object->barcode = $request->barcode;
			$object->price = $request->price;
			$object->points = $request->points ?: 0;
			$object->note = $request->note;
			$object->status = 1;
			$object->g7_id = Auth::user()->g7_id;
			$object->save();

			if ($request->image) {
				FileHelper::uploadFile($request->image, 'g7_products', $object->id, ThisModel::class, 'image');
			} else if ($object->root_product_id) {
				FileHelper::copyFile($object->root_product->image, 'g7_products', $object->id, ThisModel::class, 'image');
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

	public function update(Request $request, $id)
	{
		$object = ThisModel::findOrFail($id);

		$rule = [
			'price' => 'required|integer',
			'points' =>'nullable|integer',
			'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000',
			'status' => 'required|in:1,0'
		];

		if (!$object->root_product_id) {
			$rule = array_merge($rule, [
				'name' => [
					'required',
					Rule::unique('g7_products')->where(function ($query) use ($request) {
						return $query->where('g7_id', Auth::user()->g7_id);
					})->ignore($id),
				],
				'product_category_id' => 'required|exists:product_categories,id',
				'unit_id' => 'required|exists:units,id',
			]);
		}

		$validate = Validator::make(
			$request->all(),
			$rule
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
			if ($request->status == 0 && !$object->canDelete()) {
				$json->success = false;
				$json->message = "Không thể khóa hàng hóa này!";
				return Response::json($json);
			}

			if (!$object->root_product_id) {
				$object->name = $request->name;
				$object->product_category_id = $request->product_category_id;
				$object->unit_id = $request->unit_id;
				$object->unit_name = Unit::find($request->unit_id)->name;
			}

			$object->barcode = $request->barcode;
			$object->price = $request->price;
			$object->points = $request->points;
			$object->note = $request->note;
			$object->status = $request->status;
			$object->save();

			if ($request->image) {
				FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
				FileHelper::uploadFile($request->image, 'g7_products', $object->id, ThisModel::class, 'image');
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

	public function delete($id)
    {
		$object = ThisModel::findOrFail($id);
		if (!$object->canDelete()) {
			$message = array(
				"message" => "Không thể xóa!",
				"alert-type" => "warning"
			);
		} else {
			$object->status = 0;
			$object->save();
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
        $json->data = ThisModel::getData($id);
        return Response::json($json);
	}

    public function editPrice()
    {
        return view($this->view.'.edit-price');
    }

    public function updatePrice(Request $request)
    {
        $product_id = $request->product_id;
        $price = $request->price;
        if ($product_id && $price) {
            G7ProductPrice::updateOrCreate(
                [
                    'g7_id' => Auth::user()->g7_id,
                    'product_id' => $product_id
                ],
                ['price' => $price]
            );
        }
        $json = new stdclass();
        $json->success = true;
		$json->messages = "Cập nhật giá thành công !";
        return Response::json($json);
    }
}
