<?php

namespace App\Http\Controllers\Uptek;

use Illuminate\Http\Request;
use App\Model\Uptek\AccumulatePoint as ThisModel;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use DB;

class AccumulatePointController extends Controller
{
	protected $view = 'uptek.accumulate_points';
	protected $route = 'AccumulatePoint';

	public function edit()
	{
		$object = ThisModel::where('id',1)->first();
		return view($this->view.'.edit', compact('object'));
	}

	public function update(Request $request)
	{
		$validate = Validator::make(
			$request->all(),
			[
				'value_to_point_rate' => 'required|integer',
				'point_to_money_rate' => 'required|integer',

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
			$object = ThisModel::where('id',1)->first();
			$object->value_to_point_rate = $request->value_to_point_rate;
			$object->point_to_money_rate = $request->point_to_money_rate;
			$request->allow_pay === 'true' ? $allow_pay = 1 : $allow_pay = 0;
			$request->accumulate_pay_point === 'true' ? $accumulate_pay_point = 1 : $accumulate_pay_point = 0;
			$object->allow_pay = $allow_pay;
			$object->accumulate_pay_point = $accumulate_pay_point;

			$object->save();

			DB::commit();
			$json->success = true;
			$json->message = "Thao tác thành công!";
			return Response::json($json);
		} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
	}
}
