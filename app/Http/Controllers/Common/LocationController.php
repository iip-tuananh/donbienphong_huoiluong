<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use \stdClass;
use Response;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Vanthao03596\HCVN\Models\District;
use DB;

class LocationController extends Controller
{
	    //    Lấy danh sách huyện theo tỉnh
		public function getDistricts($id, Request $request) {
			return Response::json(array(
				'success' => true,
				'data' => District::where('parent_code', $id)->select(['id', 'name_with_type', 'parent_code'])->get()
			));
		}

		//    Lấy danh sách xã theo huyện
		public function getWards($id, Request $request) {
			return Response::json(array(
				'success' => true,
				'data' => DB::table('wards')->where('parent_code', $id)->select(['id', 'name_with_type', 'parent_code'])->get()
			));
		}
	}
