<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use App\Model\Uptek\Service;
use App\Model\Common\ServiceType;
use App\Model\Common\File;
use App\Model\Common\VehicleCategory;
use App\Model\G7\G7Product;
use Illuminate\Database\Eloquent\Model;
use DB;


class G7Service extends BaseModel
{
    protected $table = 'g7_services';

    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => 0,
            'name' => 'Khóa',
            'type' => 'danger'
        ]
    ];

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

    public function root_service()
    {
        return $this->belongsTo(Service::class, 'root_service_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(G7ServiceProduct::class, 'g7_service_id','id');
    }

    public function service_vehicle_categories()
    {
        return $this->hasMany(G7ServiceVehicleCategory::class, 'g7_service_id', 'id');
    }

    public function service_vehicle_category_products()
    {
        return $this->hasMany(G7ServiceVehicleCategoryProduct::class, 'g7_service_id', 'id');
    }

    public function canDelete() {
        return false;
    }

    public static function searchByFilter($request) {
        $result = self::where('g7_id', Auth::user()->g7_id);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->service_type)) {
            $result = $result->where('service_type_id', $request->service_type);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }



    public static function searchDataForBill($request) {
        $result = G7ServiceVehicleCategoryGroup::from('g7_service_vehicle_category_groups as gsvcp')
            ->join('g7_service_vehicle_categories as gsvc', 'gsvcp.parent_id', '=', 'gsvc.id')
            ->join('g7_services as gs', 'gsvcp.g7_service_id', '=', 'gs.id')
            ->join('service_types as st', 'gs.service_type_id', '=', 'st.id')
            ->where('gs.g7_id', Auth::user()->g7_id)
            ->where('gs.status', 1)
            ->select([
                'gsvcp.id', 'gs.name', 'gs.code', 'gsvcp.name as group_name', 'st.name as service_type', 'gsvcp.service_price as price'
            ]);

        if (!empty($request->vehicle_category_id)) {
            $result = $result->where('gsvc.vehicle_category_id', $request->vehicle_category_id);
        } else {
            $result = $result->where('gsvcp.id', -1);
        }

        if (!empty($request->name)) {
            $result = $result->where('gs.name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('gs.code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->service_type)) {
            $result = $result->where('gs.service_type_id', $request->service_type);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('gs.name', 'asc')->get();
        }

        return $result;
    }

    public static function getForSelect() {
        return self::where('g7_id', Auth::user()->g7_id)
            ->where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->with('image','service_vehicle_category_products')
            ->get();
    }

    public function syncVehicleCategories($serviceVehicleCategories) {
        G7ServiceVehicleCategoryGroupProduct::where('g7_service_id', $this->id)->delete();
        G7ServiceVehicleCategoryGroup::where('g7_service_id', $this->id)->delete();
        G7ServiceVehicleCategory::where('g7_service_id', $this->id)->delete();
        if ($serviceVehicleCategories) {
            foreach ($serviceVehicleCategories as $serviceVehicleCategory) {
                $item = new G7ServiceVehicleCategory();
                $item->g7_service_id = $this->id;
                $item->vehicle_category_id = $serviceVehicleCategory['vehicle_category_id'];
                $item->save();
                $item->syncGroups($serviceVehicleCategory['groups']);
            }
        }
    }

    public function syncProducts($serviceProducts) {
        G7ServiceProduct::where('g7_service_id', $this->id)->delete();
        if ($serviceProducts) {
            foreach ($serviceProducts as $serviceProduct) {
                $item = new G7ServiceProduct();
                $item->g7_service_id = $this->id;
                $item->product_id = $serviceProduct['g7_product_id'];
                $item->qty = $serviceProduct['qty'];
                $item->save();
            }
        }
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'products' => function($q) {
                    $q->with([
                        'product' => function($q) {
                            $q->with([
                                'category' => function($q) {
                                    $q->select(['id', 'name']);
                                }
                            ]);
                        }
                    ]);
                },
                'service_vehicle_categories' => function($q) {
                    $q->with([
                        'groups' => function($q) {
                            $q->with([
                                'products' => function($q) {
                                    $q->with([
                                        'product'
                                    ]);
                                },
                            ]);
                        }
                    ]);
                },
                'root_service',
                'image'
            ])
            ->firstOrFail();
    }

    public static function getDataForBill($service_vehicle_category_group_id) {
        return G7ServiceVehicleCategoryGroup::where('id', $service_vehicle_category_group_id)
            ->with([
                'service' => function($q) {
                    $q->select([
                        'id', 'name', 'code'
                    ]);
                },

            ])
            ->select(['*', 'service_price as price'])
            ->firstOrFail();
    }

    public function getRecipeProducts($vehicle_category_id, $group_id) {
        $object = [];
        foreach ($this->products as $p) {
            if (!isset($object[$p->g7_product_id])) $object[$p->g7_product_id] = 0;
            $object[$p->g7_product_id] += $p->qty;
        }
        $service_vehicle_category = G7ServiceVehicleCategory::where('g7_service_id', $this->id)
            ->where('vehicle_category_id', $vehicle_category_id)
            ->first();
        if ($service_vehicle_category) {
            $service_vehicle_category_group = G7ServiceVehicleCategoryGroup::where('parent_id', $service_vehicle_category->id)
                ->where('id', $group_id)
                ->first();
            if ($service_vehicle_category_group) {
                foreach ($service_vehicle_category_group->products as $p) {
                    if (!isset($object[$p->g7_product_id])) $object[$p->g7_product_id] = 0;
                    $object[$p->g7_product_id] += $p->qty;
                }
            }
        }
        return $object;
    }
}
