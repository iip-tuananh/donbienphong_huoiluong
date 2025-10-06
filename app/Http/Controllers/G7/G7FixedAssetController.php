<?php

namespace App\Http\Controllers\G7;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Model\Common\Unit;
use App\Model\G7\G7FixedAsset;
use App\Model\G7\G7FixedAsset as ThisModel;
use App\Model\G7\G7FixedAssetImportDetail;
use App\Model\Uptek\FixedAsset;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PDF;
use Response;
use stdClass;
use Validator;
use Yajra\DataTables\DataTables;

class G7FixedAssetController extends Controller
{
    protected $view = 'g7.g7_fixed_assets';
    protected $route = 'G7FixedAsset';

    public function index()
    {
        return view($this->view . '.index');
    }

    // Hàm lấy data cho bảng list
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
            ->addColumn('unit', function ($object) {
                return $object->unit ? $object->unit->name : '';
            })
            ->editColumn('price', function ($object) {
                return formatCurrency($object->import_price_quota);
            })
            ->editColumn('status', function ($object) {
                return getStatus($object->status, ThisModel::STATUSES);
            })
            // ->editColumn('image', function ($object) {
            //     return '<img style ="max-width:45px !important" src="' . $object->image->path . '"/>';
            // })
            ->addColumn('action', function ($object) {
                $result = '';
                if ($object->canView()) {
                    $result .= '<a href="javascript:void(0)" title="Xem chi tiết" class="btn show btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
                }
                if ($object->canEdit()) {
                    $result .= '<a href="' . route($this->route . '.edit',
                            $object->id) . '" title="Sửa" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a> ';
                }
                if ($object->canDelete()) {
                    $result .= '<a href="' . route($this->route . '.delete',
                            $object->id) . '" title="Xóa" class="btn btn-sm btn-danger confirm"><i class="fas fa-times"></i></a> ';
                }
                return $result;
            })
            ->rawColumns(['status', 'action', 'image'])
            ->addIndexColumn()
            ->make(true);
    }

    public function create()
    {
        return view($this->view . '.create');
    }

    public function edit($id)
    {
        $object = ThisModel::getDataForEdit($id);
        return view($this->view . '.edit', compact(['object']));
    }

    public function store(Request $request)
    {
        $rule = [
            'root_id' => 'nullable|exists:fixed_assets,id',
            'import_price_quota' => 'required',
            'depreciation_period' => 'required|integer',
        ];

        if (!$request->root_id) {
            $rule = array_merge($rule, [
                'name' => [
                    'required',
                    Rule::unique('g7_fixed_assets')->where(function ($query) use ($request) {
                        return $query->where('g7_id', Auth::user()->g7_id);
                    }),
                ],
                'code' => [
                    'required',
                    Rule::unique('g7_fixed_assets')->where(function ($query) use ($request) {
                        return $query->where('g7_id', Auth::user()->g7_id);
                    }),
                    'unique:products'
                ],
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
            if ($request->root_id) {
                $exist = ThisModel::where('root_id', $request->root_id)->where('g7_id', Auth::user()->g7_id)->first();

                if ($exist) {
                    $json->success = false;
                    $json->message = "Đã tồn tại trên hệ thống!";
                    return Response::json($json);
                }

                $root = FixedAsset::find($request->root_id);
                $object->root_id = $request->root_id;
                $object->name = $root->name;
                $object->code = $root->code;
                $object->unit_id = $root->unit_id;
                $object->unit_name = $root->unit_name;
            } else {
                $object->name = $request->name;
                $object->code = $request->code;
                $object->unit_id = $request->unit_id;
                $object->unit_name = Unit::find($request->unit_id)->name;
            }

            $object->import_price_quota = $request->import_price_quota;
            $object->depreciation_period = $request->depreciation_period;
            $object->note = $request->note;
            $object->status = 1;
            $object->g7_id = Auth::user()->g7_id;
            $object->save();

            if ($request->image) {
                FileHelper::uploadFile($request->image, 'g7_fixed_assets', $object->id, ThisModel::class, 'image', 2);
            } else {
                if ($object->root_id) {
                    FileHelper::copyFile($object->root->image, 'g7_fixed_assets', $object->id, ThisModel::class,
                        'image');
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

    public function update(Request $request, $id)
    {
        $object = ThisModel::findOrFail($id);

        $rule = [
            'import_price_quota' => 'required',
            'depreciation_period' => 'required|integer',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:3000',
            'status' => 'required|in:1,0'
        ];

        if (!$object->root_id) {
            $rule = array_merge($rule, [
                'name' => [
                    'required',
                    Rule::unique('g7_fixed_assets')->where(function ($query) use ($request) {
                        return $query->where('g7_id', Auth::user()->g7_id);
                    })->ignore($id),
                ],
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
                $json->message = "Không thể khóa tài sản này!";
                return Response::json($json);
            }

            if (!$object->root_id) {
                $object->name = $request->name;
                $object->unit_id = $request->unit_id;
                $object->unit_name = Unit::find($request->unit_id)->name;
            }

            $object->import_price_quota = $request->import_price_quota;
            $object->depreciation_period = $request->depreciation_period;
            $object->note = $request->note;
            $object->status = $request->status;
            $object->save();

            if ($request->image) {
                FileHelper::forceDeleteFiles($object->image->id, $object->id, ThisModel::class, 'image');
                FileHelper::uploadFile($request->image, 'g7_fixed_assets', $object->id, ThisModel::class, 'image', 2);
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


        return redirect()->route($this->route . '.index')->with($message);
    }

    public function getData(Request $request, $id)
    {
        return successResponse("", ThisModel::getData($id));
    }

    public function report()
    {
        $summary_report = G7FixedAssetImportDetail::getSummaryReport();
        return view($this->view . '.report', compact('summary_report'));
    }

    // Hàm lấy data cho bảng list
    public function searchReportData(Request $request)
    {
        $objects = G7FixedAssetImportDetail::searchReportByFilter($request);
        return Datatables::of($objects)
            ->editColumn('code', function ($object) {
                return $object->asset->code;
            })
            ->editColumn('name', function ($object) {
                return $object->asset->name;
            })
            ->editColumn('depreciation_period', function ($object) {
                return $object->asset->depreciation_period;
            })
            ->editColumn('import_date', function ($object) {
                return (new Carbon($object->import_date))->format('d/m/Y');
            })
            ->editColumn('total_price', function ($object) {
                return formatCurrency($object->total_price);
            })
            ->addColumn('depreciated', function ($object) {
                return formatCurrency(G7FixedAsset::getDepreciated($object->price, $object->asset->depreciation_period,
                    $object->import_date, $object->qty));
            })
            ->addColumn('residual_value', function ($object) {
                return formatCurrency($object->total_price - G7FixedAsset::getDepreciated($object->price,
                        $object->asset->depreciation_period, $object->import_date, $object->qty));
            })
            ->rawColumns(['status', 'image'])
            ->addIndexColumn()
            ->make(true);
    }
}
