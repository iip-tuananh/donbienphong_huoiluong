<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Category;
use App\Model\Admin\PolivicyDetail;
use Illuminate\Http\Request;
use App\Model\Admin\Polivicy as ThisModel;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use \stdClass;

use Rap2hpoutre\FastExcel\FastExcel;
use PDF;
use App\Http\Controllers\Controller;
use \Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Model\Common\Customer;

class PolivicyController extends Controller
{
    protected $view = 'admin.polivicy';
    protected $route = 'polivicy';

    public function edit(Request $request)
    {
        $object = ThisModel::getDataForEdit(1);

        return view($this->view.'.edit', compact('object'));
    }


    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'details' => 'array|required',
                'details.*.title' => 'required',
                'details.*.title_eng' => 'required',
                'details.*.content' => 'required',
                'details.*.content_eng' => 'required',
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
            $object = ThisModel::findOrFail(1);
            PolivicyDetail::query()->delete();
            foreach ($request->details as $d) {
                $detail = new PolivicyDetail();
                $detail->polivicy_id = $object->id;
                $detail->title = $d['title'];
                $detail->title_eng = $d['title_eng'];
                $detail->content = $d['content'];
                $detail->content_eng = $d['content_eng'];

                $detail->save();
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
        $object->delete();
        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );


        return redirect()->route($this->route . '.index')->with($message);
    }

    public function getDataForEdit($id)
    {
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
    public function exportPDF(Request $request)
    {
        $data = ThisModel::searchByFilter($request);
        $pdf = \Barryvdh\DomPDF\PDF::loadView($this->view . '.pdf', compact('data'));
        return $pdf->download('danh_sach_lich_hen.pdf');
    }


}
