<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Model\Common\PromoCampaign as ThisModel;
use App\Model\Common\User;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;
use Validator;
use App\Employee;
use Auth;
use \stdClass;
use Response;
use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use DB;
use App\Http\Traits\ResponseTrait;

class PromoCampaignController extends Controller
{

	protected $view = 'common.promo_campaigns';
	protected $route = 'PromoCampaign';

	public function index()
	{
		return view($this->view.'.index');
	}

	// Hàm phân trang, search cho datatable
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);

        return Datatables::of($objects)
			->editColumn('updated_by', function ($object) {
				return $object->user_update->name;
			})
            ->editColumn('created_at', function ($object) {
                return Carbon::parse($object->created_at)->format("d/m/Y");
            })
			->editColumn('status', function ($object) {
                return getStatus($object->status, ThisModel::STATUSES);
            })
			->editColumn('created_by', function ($object) {
                return $object->user_create ? $object->user_create->name : '';
            })
			->editColumn('start_date', function ($object) {
                return $object->start_date ? Carbon::parse($object->start_date)->format('d/m/Y') : '';
            })
			->editColumn('end_date', function ($object) {
                return $object->end_date ? Carbon::parse($object->end_date)->format('d/m/Y') : '';
            })
            ->addColumn('action', function ($object) {
				$result = '';
				if ($object->canEdit()) {
					$result = '<a href="' . route($this->route.'.edit',$object->id) .'" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
				}
				if ($object->canLock()) {
					$result .= '<a href="' . route($this->route.'.lock', $object->id) . '" title="Khóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-lock"></i></a>';
				}
				if ($object->canUnLock()) {
					$result .= '<a href="' . route($this->route.'.unlock', $object->id) . '" title="Kích hoạt" class="btn btn-sm btn-info confirm"><i class="fas fa-unlock"></i></a>';
				}
				return $result;

            })
			->rawColumns(['status', 'action'])
            ->addIndexColumn()
            ->make(true);
    }

	public function create()
	{
		return view($this->view.'.create', compact([]));
	}

	public function edit($id)
	{
		$object = ThisModel::getDataForEdit($id);
		if (!$object->canEdit()) return view('not_found');
		return view($this->view.'.edit', compact(['object']));
	}

	public function store(Request $request)
	{
		$rule = [
			'name' => 'required',
			'limit' => 'nullable|numeric|min:1|max:999999999',
			'start_date' => 'nullable|date',
			'end_date' => 'nullable|date',
			'type' => 'required|in:1,2,3',
			'checkpoints' => 'required|array|min:1',
			'checkpoints.*.from' => 'required|numeric|min:0|max:999999999',
			'checkpoints.*.products.*.qty' => 'required|numeric|min:1|max:999999999',
		];

		if ($request->type == 1) {
			$rule = array_merge($rule, [
				'checkpoints.*.value' => 'required|numeric|min:0|max:999999999',
				'checkpoints.*.to' => 'required|numeric|gt:checkpoints.*.from|max:999999999999',
				'checkpoints.*.type' => 'required|in:1,2',
			]);
		}

		if ($request->type == 2) {
			$rule = array_merge($rule, [
				'checkpoints.*.products' => 'required|array|min:1',
				'checkpoints.*.to' => 'required|numeric|gt:checkpoints.*.from|max:999999999999',
			]);
		}

		if ($request->type == 3) {
			$rule = array_merge($rule, [
				'product_id' => 'required|exists:products,id',
				'checkpoints' => 'required|array|size:1',
				'checkpoints.*.products' => 'required|array|min:1',
			]);
		}

		if (auth()->user()->type != User::G7) {
			$rule = array_merge($rule, [
				'g7_ids' => 'required_unless:for_all,true|nullable|array|min:1',
			]);
		}

		$validate = Validator::make(
			$request->all(),
			$rule,
			[]
		);

		if ($validate->fails()) {
			return errorResponse("", $validate->errors());
		}

		DB::beginTransaction();
		try {
			$object = new ThisModel();
			$object->name = $request->name;
			$object->code = randomString(10);
			$object->type = $request->type;
			$object->product_id = $request->product_id;
			$object->note = $request->note;
			$object->start_date = $request->start_date;
			$object->end_date = $request->end_date;
			$object->limit = $request->limit;
			if (auth()->user()->type != User::G7) $object->for_all = $request->for_all == 'true';
			$object->save();
			$object->generateCode();

			$object->syncCheckpoints($request->checkpoints);
			if (auth()->user()->type == User::G7) $object->g7s()->sync([auth()->user()->g7_id]);
			else if ($object->for_all) $object->g7s()->sync($request->g7_ids);

			DB::commit();
			return successResponse();
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function update(Request $request, $id)
	{
		$object = ThisModel::findOrFail($id);
		if (!$object->canEdit()) return errorResponse('Không có quyền');

		$rule = [
			'name' => 'required',
			'limit' => 'nullable|numeric|min:1|max:999999999',
			'start_date' => 'nullable|date',
			'end_date' => 'nullable|date',
			'type' => 'required|in:1,2,3',
			'checkpoints' => 'required|array|min:1',
			'checkpoints.*.from' => 'required|numeric|min:0|max:999999999',
			'checkpoints.*.products.*.qty' => 'required|numeric|min:1|max:999999999',
		];

		if ($request->type == 1) {
			$rule = array_merge($rule, [
				'checkpoints.*.value' => 'required|numeric|min:0|max:999999999',
				'checkpoints.*.to' => 'required|numeric|gt:checkpoints.*.from|max:999999999999',
				'checkpoints.*.type' => 'required|in:1,2',
			]);
		}

		if ($request->type == 2) {
			$rule = array_merge($rule, [
				'checkpoints.*.products' => 'required|array|min:1',
				'checkpoints.*.to' => 'required|numeric|gt:checkpoints.*.from|max:999999999999',
			]);
		}

		if ($request->type == 3) {
			$rule = array_merge($rule, [
				'product_id' => 'required|exists:products,id',
				'checkpoints' => 'required|array|size:1',
				'checkpoints.*.products' => 'required|array|min:1',
			]);
		}

		if (auth()->user()->type != User::G7) {
			$rule = array_merge($rule, [
				'g7_ids' => 'required_unless:for_all,true|nullable|array|min:1',
			]);
		}

		$validate = Validator::make(
			$request->all(),
			$rule,
			[]
		);

		if ($validate->fails()) {
			return errorResponse("", $validate->errors());
		}

		DB::beginTransaction();
		try {

			$object->name = $request->name;
			$object->type = $request->type;
			$object->product_id = $request->product_id;
			$object->note = $request->note;
			$object->start_date = $request->start_date;
			$object->end_date = $request->end_date;
			$object->limit = $request->limit;
			if (auth()->user()->type != User::G7) $object->for_all = $request->for_all == 'true';
			$object->save();

			$object->syncCheckpoints($request->checkpoints);
			if (auth()->user()->type == User::G7) $object->g7s()->sync([auth()->user()->g7_id]);
			else if ($object->for_all) $object->g7s()->sync($request->g7_ids);

			DB::commit();
			return successResponse();
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}

	public function lock($id)
    {
		$object = ThisModel::findOrFail($id);
		if (!$object->canLock()) {
			$message = array(
				"message" => "Không thể khóa!",
				"alert-type" => "warning"
			);
		} else {
			$object->status = ThisModel::LOCK;
			$object->save();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		}
        return redirect()->route($this->route.'.index')->with($message);
	}

	public function unlock($id)
    {
		$object = ThisModel::findOrFail($id);
		if (!$object->canUnLock()) {
			$message = array(
				"message" => "Không thể kích hoạt!",
				"alert-type" => "warning"
			);
		} else {
			$object->status = ThisModel::ACTIVE;
			$object->save();
			$message = array(
				"message" => "Thao tác thành công!",
				"alert-type" => "success"
			);
		}
        return redirect()->route($this->route.'.index')->with($message);
	}

	public function getDataForUse(Request $request, $id) {
		$json = new stdClass();

		$object = ThisModel::findOrFail($id);
		if (!$object->canUse()) return errorResponse('Không thể áp dụng CTKM này');

		return successResponse("", ThisModel::getDataForUse($id));
	}
}
