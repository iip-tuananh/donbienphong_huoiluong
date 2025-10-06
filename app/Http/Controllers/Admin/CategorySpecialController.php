<?php

namespace App\Http\Controllers\Admin;

use App\Model\Admin\Category;
use App\Model\Admin\CategorySpecial;
use App\Model\Admin\PostCategorySpecial;
use App\Model\Admin\ProductCategorySpecial;
use App\Model\Admin\TourCategorySpecial;
use Illuminate\Http\Request;
use App\Model\Admin\CategorySpecial as ThisModel;
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

class CategorySpecialController extends Controller
{
    protected $view = 'admin.category_special';
    protected $route = 'category_special';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = ThisModel::searchByFilter($request);
        return Datatables::of($objects)
            ->editColumn('updated_by', function ($object) {
                return $object->user_update->name ?? '';
            })
            ->editColumn('created_by', function ($object) {
                return $object->user_update->name ?? '';
            })
            ->editColumn('order_number', function ($object) {
                $n = (int) ($object->order_number ?? 0);
                return '<span class="badge badge-primary text-nowrap" data-order="'.$n.'">'.$n.'</span>';
            })
            ->editColumn('show_home_page', function ($object) {
                $v = (int) $object->show_home_page;
                $label = $v ? 'Hiển thị' : 'Không';
                $icon  = $v ? 'fa-check' : 'fa-times';
                $class = $v ? 'badge-success' : 'badge-secondary';
                return '<span class="badge '.$class.' text-nowrap" data-order="'.$v.'">
                <i class="fas '.$icon.' mr-1"></i>'.$label.'
            </span>';
            })
            ->editColumn('end_date', function ($object) {
                return $object->end_date ? formatDate($object->end_date) : '';
            })
            ->editColumn('updated_at', function ($object) {
                return formatDate($object->updated_at);
            })
            ->addColumn('action', function ($object) {
                $result = '';
                $result .= '<a href="javascript:void(0)" title="Sửa" class="btn btn-sm btn-primary edit"><i class="fas fa-pencil-alt"></i></a> ';
                if($object->id == 11) {
                } else {
                    $result .= '<a href="' . route($this->route.'.delete', $object->id) . '" title="Xóa" class="btn btn-sm btn-danger remove"><i class="fas fa-times"></i></a>';
                }

                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['name', 'action', 'order_number', 'show_home_page'])
            ->make(true);
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function store(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:category_special,name',
                'show_home_page' => 'required',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
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


            $object->name = $request->name;
            $object->order_number = $request->order_number;
            $object->show_home_page = $request->show_home_page;
            $object->save();

            if ($request->image) {
                // FileHelper::uploadFile($request->image, 'category_special', $object->id, ThisModel::class, 'image', 99);
                FileHelper::uploadFileToCloudflare($request->image, $object->id, ThisModel::class, 'image');
            }

            DB::commit();
            $json->success = true;
            $json->message = "Thao tác thành công!";
            $json->data = $object;
            return Response::json($json);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        $object = ThisModel::findOrFail($id);
        if (!$object->canview()) return view('not_found');
        $object = ThisModel::getDataForShow($id);
        return view($this->view . '.show', compact('object'));
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:category_special,name,'.$id.",id",
                'show_home_page' => 'required',
                'image' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
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
            $object->name = $request->name;
            $object->order_number = $request->order_number;
            $object->show_home_page = $request->show_home_page;
            $object->save();

            if ($request->image) {

                if ($object->image) {
                    // FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
                    FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
                }

                // FileHelper::uploadFile($request->image, 'category_special', $object->id, ThisModel::class, 'image', 99);
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
                "message" => "Không thể xóa!",
                "alert-type" => "warning"
            );
        } else {
            // xóa sản phẩm hoặc bài viết ra khỏi danh mục này
            PostCategorySpecial::query()->where('category_special_id', $id)->delete();

            if($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, ThisModel::class, 'image');
            }

            $object->delete();

            $message = array(
                "message" => "Thao tác thành công!",
                "alert-type" => "success"
            );
        }


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
