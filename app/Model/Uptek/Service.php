<?php

namespace App\Model\Uptek;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\User;
use App\Model\Common\ServiceType;
use App\Model\Common\VehicleCategory;
use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use App\Model\Common\File;
use App\Model\G7\BillService;

class Service extends BaseModel
{
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

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

    public function bills()
    {
        return $this->hasMany(BillService::class,'service_id','id');
    }

	public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function products()
    {
        return $this->hasMany(ServiceProduct::class,'service_id','id');
    }

    public function service_vehicle_categories()
    {
        return $this->hasMany(ServiceVehicleCategory::class, 'service_id', 'id');
    }

	public function service_vehicle_category_groups()
    {
        return $this->hasMany(ServiceVehicleCategoryGroup::class, 'service_id', 'id');
    }

    public function service_vehicle_category_group_products()
    {
        return $this->hasMany(ServiceVehicleCategoryGroupProduct::class, 'service_id', 'id');
    }

    public function canDelete() {
        if(Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN) {
            if($this->bills->count() > 0) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    public function canEdit()
    {
        return Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN;
    }

    public static function searchByFilter($request) {
        $result = self::query();

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

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public function syncVehicleCategories($serviceVehicleCategories) {
		$serviceVehicleCategories = $serviceVehicleCategories ?: [];
        $ids = array_map('getId', $serviceVehicleCategories);
		$delete_categories = ServiceVehicleCategory::where('service_id', $this->id)->whereNotIn('id', $ids)->get();
		foreach ($delete_categories as $c) {
			$c->removeFromDB();
		}
		foreach ($serviceVehicleCategories as $serviceVehicleCategory) {
			if (isset($serviceVehicleCategory['id'])) $item = ServiceVehicleCategory::where('service_id', $this->id)->where('id', $serviceVehicleCategory['id'])->first();
			else $item = new ServiceVehicleCategory();
			$item->service_id = $this->id;
			$item->vehicle_category_id = $serviceVehicleCategory['vehicle_category_id'];
			$item->save();
			$item->syncGroups($serviceVehicleCategory['groups']);
		}
    }

    public function syncProducts($serviceProducts) {
        ServiceProduct::where('service_id', $this->id)->delete();
        if ($serviceProducts) {
            foreach ($serviceProducts as $serviceProduct) {
                $item = new ServiceProduct();
                $item->service_id = $this->id;
                $item->product_id = $serviceProduct['product_id'];
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
                'image'
            ])
            ->firstOrFail();
    }

    // public function canG7Use() {
    //     $json = new stdClass();
    //     $json->success = false;

    //     if (Auth::user()->type != User::G7 || !Auth::user()->g7_id) {
	// 		$json->message = "Không phải tài khoản G7";
	// 		return $json;
	// 	}

    //     foreach ($this->products as $p) {
    //         $g7_product = G7Product::where('root_product_id', $p->product_id)
    //             ->where('g7_id', Auth::user()->g7_id)
    //             ->first();

    //         if (!$g7_product) {
    //             $json->message = "Cần cài đặt hàng hóa ".$p->product->code." - ".$p->product->name;
    //             return $json;
    //         }
    //     }

    //     foreach ($this->service_vehicle_category_group_products as $p) {
    //         $g7_product = G7Product::where('root_product_id', $p->product_id)
    //             ->where('g7_id', Auth::user()->g7_id)
    //             ->first();

    //         if (!$g7_product) {
    //             $json->message = "Cần cài đặt hàng hóa ".$p->product->code." - ".$p->product->name;
    //             return $json;
    //         }
    //     }

    //     $json->success = true;
    //     return $json;
    // }

    // public static function getDataForG7Service($id) {
    //     $service = self::getDataForEdit($id);

    //     $result = $service->toArray();

    //     $result['products'] = [];
    //     foreach ($result['service_vehicle_categories'] as $index => $svc) {
    //         foreach ($svc['groups'] as $ig => $g) {
    //             $result['service_vehicle_categories'][$index]['groups'][$ig]['products'] = [];
    //         }
    //     }

    //     foreach ($service->products as $p) {
    //         $g7_product = G7Product::where('root_product_id', $p->product_id)
    //             ->where('g7_id', Auth::user()->g7_id)
    //             ->first();
    //         array_push($result['products'], [
    //             'g7_product_id' => $g7_product->id,
    //             'qty' => $p->qty,
    //             'product' => G7Product::getData($g7_product->id)
    //         ]);
    //     }

    //     foreach ($service->service_vehicle_categories as $index => $svc) {
    //         foreach ($svc->groups as $ig => $g) {
    //             foreach ($g->products as $p) {
    //                 $g7_product = G7Product::where('root_product_id', $p->product_id)
    //                     ->where('g7_id', Auth::user()->g7_id)
    //                     ->first();
    //                 array_push($result['service_vehicle_categories'][$index]['groups'][$ig]['products'], [
    //                     'g7_product_id' => $g7_product->id,
    //                     'service_price' => $p->service_price,
    //                     'qty' => $p->qty,
    //                     'product' => G7Product::getData($g7_product->id)
    //                 ]);
    //             }
    //         }
    //     }

    //     return $result;
    // }

	public static function searchDataForBill($request) {
        $result = ServiceVehicleCategoryGroup::from('service_vehicle_category_groups as gsvcp')
            ->join('service_vehicle_categories as gsvc', 'gsvcp.parent_id', '=', 'gsvc.id')
            ->join('services as gs', 'gsvcp.service_id', '=', 'gs.id')
            ->join('service_types as st', 'gs.service_type_id', '=', 'st.id')
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

	public static function searchAllForBill($vehicle_category_id) {
        $result = ServiceVehicleCategoryGroup::from('service_vehicle_category_groups as gsvcp')
            ->join('service_vehicle_categories as gsvc', 'gsvcp.parent_id', '=', 'gsvc.id')
            ->join('services as gs', 'gsvcp.service_id', '=', 'gs.id')
            ->where('gs.status', 1)
			->where('gsvc.vehicle_category_id', $vehicle_category_id)
            ->orderBy('gs.name', 'asc')
            ->select(['gsvcp.*'])
			->with([
				'service' => function($q) {
					$q->with([
						'image',
                        'service_type'
					]);
				}
			])
			->get();

        return $result;
    }

	public static function getDataForBill($service_vehicle_category_group_id) {
        return ServiceVehicleCategoryGroup::where('id', $service_vehicle_category_group_id)
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
            if (!isset($object[$p->product_id])) $object[$p->product_id] = 0;
            $object[$p->product_id] += $p->qty;
        }
        $service_vehicle_category = ServiceVehicleCategory::where('service_id', $this->id)
            ->where('vehicle_category_id', $vehicle_category_id)
            ->first();
        if ($service_vehicle_category) {
            $service_vehicle_category_group = ServiceVehicleCategoryGroup::where('parent_id', $service_vehicle_category->id)
                ->where('id', $group_id)
                ->first();
            if ($service_vehicle_category_group) {
                foreach ($service_vehicle_category_group->products as $p) {
                    if (!isset($object[$p->product_id])) $object[$p->product_id] = 0;
                    $object[$p->product_id] += $p->qty;
                }
            }
        }
        return $object;
    }

    public function generateCode() {
        $this->code = "DV-".generateCode(6, $this->id);
        $this->save();
    }
}
