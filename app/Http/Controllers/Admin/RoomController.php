<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Rooms\RoomStoreRequest;
use App\Http\Requests\Rooms\RoomUpdateRequest;
use App\Model\Admin\Room;
use Illuminate\Http\Request;
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

class RoomController extends Controller
{
    private $view = 'admin.rooms';
    private $route = 'Room';

    public function index()
    {
        return view('admin.rooms.index');
    }

    // Hàm lấy data cho bảng list
    public function searchData(Request $request)
    {
        $objects = Room::searchByFilter($request);
        return Datatables::of($objects)
            ->addColumn('name', function ($object) {
                return $object->name;
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
                return $object->category_specials->implode('name', ', ');
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

                $result = $result . '</div></div>';
                return $result;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view($this->view.'.create');
    }

    public function store(RoomStoreRequest $request)
    {
        $json = new stdClass();
        DB::beginTransaction();
        try {
            $object = new Room();
            $object->name = $request->name;
            $object->name_eng = $request->name_eng;
            $object->area = $request->area;
            $object->area_eng = $request->area_eng;
            $object->view = $request->view;
            $object->view_eng = $request->view_eng;
            $object->bedrooms = $request->bedrooms;
            $object->bedrooms_eng = $request->bedrooms_eng;
            $object->description = $request->description;
            $object->description_eng = $request->description_eng;
            $object->highlight = $request->highlight;
            $object->highlight_eng = $request->highlight_eng;
            $object->amenities = $request->amenities;
            $object->amenities_eng = $request->amenities_eng;
            $object->maximum_occupancy = $request->maximum_occupancy;
            $object->maximum_occupancy_eng = $request->maximum_occupancy_eng;
            $object->status = $request->status;

            $object->save();

            $object->syncGalleries($request->galleries);


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
        $object = Room::getDataForEdit($id);

        return view($this->view.'.edit', compact('object'));
    }

    public function update(RoomUpdateRequest $request, $id)
    {
        $json = new stdClass();

        DB::beginTransaction();
        try {
            $object = Room::findOrFail($id);

            if (!$object->canEdit()) {
                $json->success = false;
                $json->message = "Bạn không có quyền sửa hàng hóa này";
                return Response::json($json);
            }

            $object->name = $request->name;
            $object->name_eng = $request->name_eng;
            $object->area = $request->area;
            $object->area_eng = $request->area_eng;
            $object->view = $request->view;
            $object->view_eng = $request->view_eng;
            $object->bedrooms = $request->bedrooms;
            $object->bedrooms_eng = $request->bedrooms_eng;
            $object->description = $request->description;
            $object->description_eng = $request->description_eng;
            $object->highlight = $request->highlight;
            $object->highlight_eng = $request->highlight_eng;
            $object->amenities = $request->amenities;
            $object->amenities_eng = $request->amenities_eng;
            $object->maximum_occupancy = $request->maximum_occupancy;
            $object->maximum_occupancy_eng = $request->maximum_occupancy_eng;
            $object->status = $request->status;

            $object->save();

            $object->syncGalleries($request->galleries);


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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $object = Room::findOrFail($id);
        if (!$object->canDelete()) {
            $message = array(
                "message" => "Không thể xóa!",
                "alert-type" => "warning"
            );
        } else {
            if($object->image) {
                FileHelper::deleteFileFromCloudflare($object->image, $object->id, Room::class, 'image');
            }
            if($object->image_back) {
                FileHelper::deleteFileFromCloudflare($object->image_back, $object->id, Room::class, 'image_back');
            }
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
        $json->data = Room::getDataForEdit($id);
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
        $tour = Room::query()->find($request->tour_id);

        $tour->category_specials()->sync($request->category_special_ids);

        return Response::json(['success' => true, 'message' => 'Thao tác thành công']);
    }

    // xóa nhiều sản phẩm
    public function actDelete(Request $request) {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $product_ids = explode(',', $request->product_ids);
        foreach ($product_ids as $product_id) {
            $product = Room::findOrFail($product_id);
            if($product->image) {
                FileHelper::deleteFileFromCloudflare($product->image, $product->id, Room::class, 'image');
            }
        }

        Room::query()->whereIn('id', $product_ids)->delete();

        $message = array(
            "message" => "Thao tác thành công!",
            "alert-type" => "success"
        );

        return redirect()->route($this->route.'.index')->with($message);
    }

    public function deleteFile(Request $request, $id) {
        $json = new \stdClass();
        $req = Room::findOrFail($id);

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
