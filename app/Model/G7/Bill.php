<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\Car;
use App\Model\Common\User;
use App\Model\Common\ActivityLog;
use App\Model\Uptek\Service;
use App\Model\Uptek\ServiceVehicleCategoryGroup;
use App\Model\Product;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Model\Uptek\PrintTemplate;
use App\Model\Common\PromoCampaign;

class Bill extends BaseModel
{
    protected $table = 'bills';


    public CONST DA_DUYET = 1;
    public CONST DANG_TAO = 3;
    public CONST DA_CHOT = 2;
    public CONST DA_HUY = 0;

    public CONST STATUSES = [
        [
            'id' => self::DA_DUYET,
            'name' => 'Đã duyệt',
            'type' => 'success'
        ],
        [
            'id' => self::DA_CHOT,
            'name' => 'Đã chốt',
            'type' => 'success'
        ],
        [
            'id' => self::DANG_TAO,
            'name' => 'Đang tạo',
            'type' => 'danger'
        ],
        [
            'id' => self::DA_HUY,
            'name' => 'Đã hủy',
            'type' => 'danger'
        ]
    ];

    public function canPay()
    {
        return $this->g7_id == Auth::user()->g7_id && ($this->status == self::DA_DUYET || $this->status == self::DA_CHOT);
    }

	public function getListAttribute() {
		$products = $this->products;
        $services = $this->services;
		$others = $this->others;

        return $products->merge($services)->merge($others)->sortBy('index');
	}

    public function getPrintDataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrintHeader();
        $result['SO_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->bill_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['BIEN_SO_XE'] = $this->license_plate;
        $result['KHACH_HANG'] = $this->customer_name;
        $result['TONG_GIA_TRI'] = formatCurrency($this->cost_after_vat);
        $result['GHI_CHU'] = $this->note;

        $result['CHI_TIET_HOA_DON'] = $this->detail_table;
        $result['TONG_HOP'] = $this->bill_footer;

        return $result;
    }

    public function getPrint57DataAttribute()
    {
        $result = [];
        $result['invoice_header'] = getPrint57Header();
        $result['SO_PHIEU'] = $this->code;
        $result['NGAY_GHI_NHAN'] = \Carbon\Carbon::parse($this->bill_date)->format('H:m d/m/Y');
        $result['NGUOI_LAP'] = $this->user_create->name;
        $result['TRANG_THAI'] = getStatus($this->status, self::STATUSES);
        $result['BIEN_SO_XE'] = $this->license_plate;
        $result['KHACH_HANG'] = $this->customer_name;
        $result['DIEN_THOAI_KHACH'] = $this->customer->mobile;
        $result['TONG_GIA_TRI'] = formatCurrency($this->cost_after_vat);
        $result['GHI_CHU'] = $this->note;

        $result['CHI_TIET_HOA_DON'] = $this->detail_57;
        $result['TONG_HOP'] = $this->bill_footer;

        return $result;
    }


    public function getDetailTableAttribute()
    {
        $data = '';

        $bill_detail = $this->list;

        foreach($bill_detail as $detail) {
            $data .= '<tr>';
            $data .= '<td style="text-align: center;">'.($detail->index + 1).'</td>';
            if($detail->service_id) {
                $data .= '<td style="text-align: center;">'.$detail->name.'<div><b>Gói vật tư:</b>'.$detail->group_name.'</div></td>';
            } else {
                $data .= '<td style="text-align: center;">'.$detail->name.'</td>';
            }
            if($detail->service_id) {
                $data .= '<td style="text-align: center;">Gói</td>';
            } else {
                $data .= '<td style="text-align: center;">'.$detail->unit_name.'</td>';
            }
            $data .= '<td style="text-align: center;">'.$detail->qty.'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price * $detail->qty).'</td>';
            $data .= '</tr>';
        }
		if (count($this->promo_products)) {
			$data .= '<tr>
				<td colspan="6">Khuyến mãi</td>
			</tr>';
			foreach($this->promo_products as $i => $p) {
				$data .= '<tr>';
				$data .= '<td style="text-align: center;">'.(count($bill_detail) + $i + 1).'</td>';
				$data .= '<td style="text-align: center;">'.$p->product_name.'</td>';
				$data .= '<td style="text-align: center;">'.$p->unit_name.'</td>';
				$data .= '<td style="text-align: center;">'.$p->qty.'</td>';
				$data .= '<td style="text-align: center;">0</td>';
				$data .= '<td style="text-align: center;">0</td>';
				$data .= '</tr>';
			}
		}

        $result = '<table style="width: 100%;">
            <thead>
                <tr>
                <td style="text-align: center"><b>STT</b></td>
                <td style="text-align: center" ><b>Tên</b></td>
                <td style="text-align: center"><b>Đơn vị</b></td>
                <td style="text-align: center"><b>Số lượng</b></td>
                <td style="text-align: center"><b>Đơn giá</b></td>
                <td style="text-align: center"><b>Thành tiền</b></td>
                </tr>
            </thead>
            <tbody>'
            .$data.
            '</tbody>
        </table>';

        return $result;
    }

    public function getDetail57Attribute()
    {
        $data = '';

        $bill_detail = $this->list;

        foreach($bill_detail as $detail) {
            if($detail->service_id) {
                $data .= '<tr><td colspan="3" style="">'. ($detail->index + 1).'-'.$detail->name.' : '.$detail->group_name .'</td></tr>';
            } else {
                $data .= '<tr><td colspan="3" style="border:none">'. ($detail->index + 1).'-'.$detail->name.'</td></tr>';
            }
            $data .= '<tr>';
            if($detail->service_id) {
                $data .= '<td style="text-align: center;">';
                $data .= '<p>'.formatCurrency($detail->qty).'.<b>Gói</b></p>';
                $data .= '</td>';
            } else {
                $data .= '<td style="text-align: center; ">';
                $data .= '<p>'.formatCurrency($detail->qty).'.<b>'.$detail->unit_name.'</b></p>';
                $data .= '</td>';
            }
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price).'</td>';
            $data .= '<td style="text-align: center;">'.formatCurrency($detail->price * $detail->qty).'</td>';
            $data .= '</tr>';
            $data .= '<tr><td colspan="3"><div style="width:100%;border-top: 1px dashed #ccc;"></div></td></tr>';
        }
		if (count($this->promo_products)) {
			$data .= '<tr>
				<td colspan="6">Khuyến mãi</td>
			</tr>';
			foreach($this->promo_products as $i => $p) {
				$data .= '<tr>';
				$data .= '<td style="text-align: center;">'.(count($bill_detail) + $i + 1).'</td>';
				$data .= '<td style="text-align: center;">'.$p->product_name.'</td>';
				$data .= '<td style="text-align: center;">'.$p->unit_name.'</td>';
				$data .= '<td style="text-align: center;">'.$p->qty.'</td>';
				$data .= '<td style="text-align: center;">0</td>';
				$data .= '<td style="text-align: center;">0</td>';
				$data .= '</tr>';
			}
		}

        $result = '<style>table tr td {border: none !important;padding: 3px !important;font-size: 10px;}</style><table style="width: 100%; border:none">
            <thead>
                <tr>
                    <td style="text-align: center" ><b>Số lượng/Đơn vị</b></td>
                    <td style="text-align: center"><b>Đơn giá</b></td>
                    <td style="text-align: center"><b>Thành tiền</b></td>
                </tr>
                <tr><td colspan="3"><div style="width:100%;border-top: 1px solid #ccc;"></div></td></tr>
            </thead>
            <tbody>'
            .$data.
            '</tbody>
        </table>';

        return $result;
    }

    public function getBillFooterAttribute()
    {
        $result = '<table style="width: 100%; margin-top: 10px"><tbody>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">Tổng giá trị</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->cost_after_sale).'</b></td>';
        $result .= '</tr>';
		if ($this->promo_value > 0) {
			$result .= '<tr>';
			$result .= '<td style="text-align: left">Giá trị sau chiết khấu</td>';
			$result .= '<td style="text-align: right"><b>'.formatCurrency($this->cost_after_promo).'</b></td>';
			$result .= '</tr>';
		}
        $result .= '<tr>';
        $result .= '<td style="text-align: left">VAT('.$this->vat_percent.'%)</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->vat_cost).'</b></td>';
        $result .= '</tr>';
        $result .= '<tr>';
        $result .= '<td style="text-align: left">Tổng giá trị sau VAT</td>';
        $result .= '<td style="text-align: right"><b>'.formatCurrency($this->cost_after_vat).'</b></td>';
        $result .= '</tr>';
        if($this->point_money) {
            $result .= '<tr>';
            $result .= '<td style="text-align: left">Thanh toán bằng điểm</td>';
            $result .= '<td style="text-align: right"><b>'.formatCurrency($this->point_money).'</b></td>';
            $result .= '</tr>';
            $result .= '<tr>';
            $result .= '<tr>';
            $result .= '<td style="text-align: left">Giá trị còn lại</td>';
            $result .= '<td style="text-align: right"><b>'.formatCurrency($this->cost_after_vat - $this->point_money).'</b></td>';
            $result .= '</tr>';
            $result .= '<tr>';
        }
        $result .= '</tbody></table>';
        return $result;
    }

	public function print($type) {
		if ($type == '57') {
            $template = PrintTemplate::where('id', PrintTemplate::HOA_DON_BAN_57)->firstOrFail();
            $template = self::fillReport($template->template, $this->print_57_data);
        } else {
            $template = PrintTemplate::where('id', PrintTemplate::HOA_DON_BAN)->firstOrFail();
            $template = self::fillReport($template->template, $this->print_data);
        }

        $template = self::clearNull($template);
        if ($type == '57') return view('print57', compact('template'));
		else return view('print', compact('template'));
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

	public function promo()
    {
        return $this->belongsTo(PromoCampaign::class, 'promo_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(BillProduct::class, 'parent_id','id');
    }

	public function promo_products()
    {
        return $this->hasMany(BillPromoProduct::class, 'parent_id','id');
    }

    public function warehouseExport()
    {
        return $this->belongsTo(WarehouseExport::class,'id','bill_id');
    }

    public function export_products()
    {
        return $this->hasMany(BillExportProduct::class, 'parent_id', 'id');
    }

    public function services()
    {
        return $this->hasMany(BillService::class, 'parent_id','id');
    }

	public function others()
    {
        return $this->hasMany(BillOther::class, 'parent_id','id');
    }

    public function canDelete() {
        return $this->status == self::DANG_TAO && Auth::user()->id == $this->created_by;
    }

    public function canEdit() {
        return $this->status == self::DANG_TAO && Auth::user()->id == $this->created_by;
    }

    public function canView() {
        return Auth::user()->id == $this->created_by || Auth::user()->is_super_admin;
    }

	public function canExport() {
        return $this->status == self::DA_DUYET && in_array(Auth::user()->type, [User::G7]) && Auth::user()->g7_id == $this->g7_id;
    }

	public function canReturn() {
        return $this->status == self::DA_CHOT && in_array(Auth::user()->type, [User::G7]) && Auth::user()->g7_id == $this->g7_id;
    }

    public static function searchByFilter($request) {
        $result = self::query();

        if (Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN) {

        } else if (Auth::user()->type == User::NHOM_G7) {

        } else $result = $result->where('g7_id', Auth::user()->g7_id);

		if ($request->type == 'export') {
			$result = $result->where('status', self::DA_DUYET);
		} else if ($request->type == 'return') {
			$result = $result->where('status', self::DA_CHOT)->where('g7_id', Auth::user()->g7_id);
		}

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->license_plate)) {
            $result = $result->where('license_plate', 'like', '%'.$request->license_plate.'%');
        }

        if (!empty($request->customer_name)) {
            $result = $result->where('customer_name', 'like', '%'.$request->customer_name.'%');
        }

        if (!empty($request->created_by)) {
            $result = $result->where('created_by', $request->created_by);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }

    public function syncList($list) {
        BillProduct::where('parent_id', $this->id)->delete();
        BillService::where('parent_id', $this->id)->delete();
		BillOther::where('parent_id', $this->id)->delete();
        if ($list) {
            foreach ($list as $index => $l) {
                if (setDefault($l, 'is_service', 'false') == 'true') {
                    $service = Service::find($l['service_id']);
                    $group = ServiceVehicleCategoryGroup::find($l['group_id']);
                    $item = new BillService();
                    $item->parent_id = $this->id;
                    $item->service_id = $l['service_id'];
                    $item->name = $service->name;
                    $item->code = $service->code;
                    $item->group_id = $l['group_id'];
                    $item->group_name = $group->name;
                    $item->qty = $l['qty'];
                    $item->price = $l['price'];
                    $item->total_cost = $item->price * $item->qty;
                    $item->index = $index;
                    $item->save();
                } else if (setDefault($l, 'is_product', 'false') == 'true') {
                    $product = Product::find($l['product_id']);
                    $item = new BillProduct();
                    $item->parent_id = $this->id;
                    $item->product_id = $l['product_id'];
                    $item->name = $product->name;
                    $item->code = $product->code;
                    $item->unit_id = $product->unit_id;
                    $item->unit_name = $product->unit_name;
                    $item->qty = $l['qty'];
                    $item->price = $l['price'];
                    $item->total_cost = $item->price * $item->qty;
                    $item->index = $index;
                    $item->save();
                } else {
					$item = new BillOther();
                    $item->parent_id = $this->id;
                    $item->name = $l['name'];
                    $item->qty = $l['qty'];
                    $item->price = $l['price'];
                    $item->total_cost = $item->price * $item->qty;
                    $item->index = $index;
                    $item->save();
				}
            }
        }
    }

	public function syncPromoProducts($list) {
        BillPromoProduct::where('parent_id', $this->id)->delete();
        if ($list) {
            foreach ($list as $index => $l) {
				$product = Product::find($l['product_id']);
				$item = new BillPromoProduct();
				$item->parent_id = $this->id;
				$item->product_id = $l['product_id'];
				$item->product_name = $product->name;
				$item->code = $product->code;
				$item->unit_id = $product->unit_id;
				$item->unit_name = $product->unit_name;
				$item->qty = $l['qty'];
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
								'g7_price'
							]);
						},
                    ]);
                },
				'others',
                'services' => function($q) {
                    $q->with([
                        'service',
                        'group'
                    ]);
                },
                'car' => function($q) {
                    $q->select(['id','license_plate as name'])->with([
                        'customers'
                    ]);
                },
                'customer',
				'promo' => function($q) {
					$q->with([
						'checkpoints' => function($q) {
							$q->with([
								'products' => function($q) {
									$q->with([
										'product'
									]);
								}
							]);
						},
					]);
				},
				'promo_products'
            ])
            ->firstOrFail();
    }

	public static function getDataForReturn($id) {
        return self::where('id', $id)
            ->with([
                'products' => function($q) {
                    $q->select(['*', DB::raw('(exported_qty - returned_qty) as qty'), 'export_price as import_price']);
                },
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'products',
                'services',
				'others',
				'promo',
				'promo_products',
                'warehouseExport'
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "HĐ-".generateCode(5, $this->id);
        $this->save();
    }

    public function approve() {
		ActivityLog::createRecord("Tạo hóa đơn dịch vụ", route('Bill.show', $this->id, false));
        $object = [];
        foreach ($this->products as $p) {
            if (!isset($object[$p->product_id])) $object[$p->product_id] = 0;
            $object[$p->product_id] += $p->qty;
        }

		foreach ($this->promo_products as $p) {
            if (!isset($object[$p->product_id])) $object[$p->product_id] = 0;
            $object[$p->product_id] += $p->qty;
        }

		if ($this->vehicle_category_id) {
			foreach ($this->services as $s) {
				$service = Service::find($s->service_id);
				foreach ($service->getRecipeProducts($this->vehicle_category_id, $s->group_id) as $key => $value) {
					if (!isset($object[$key])) $object[$key] = 0;
					$object[$key] += $value * $s->qty;
				}
			}
		}

		foreach ($object as $key => $value) {
            $product = Product::find($key);
            $export_product = new BillExportProduct();
            $export_product->parent_id = $this->id;
            $export_product->product_id = $key;
            $export_product->unit_id = $product->unit_id;
            $export_product->unit_name = $product->unit_name;
            $export_product->qty = $value;
            $export_product->real_qty = $value;
            $export_product->save();

            // $stock = Stock::where('product_id', $key)->where('g7_id', $this->g7_id)->first();
            // if (!$stock) {
            //     $stock = new Stock();
            //     $stock->g7_id = $this->g7_id;
            //     $stock->product_id = $key;
            //     $stock->qty = 0;
            //     $stock->save();
            // }

            // $log = new StockLog();
            // $log->stock_id = $stock->id;
            // $log->bill_export_product_id = $export_product->id;
            // $log->qty_before = $stock->qty;
            // $log->change = -$export_product->qty;
            // $log->qty_after = $stock->qty - $export_product->qty;
            // $log->save();

            // $stock->qty -= $export_product->qty;
            // $stock->save();
		}
    }

    public static function getDataForFinalAdjust() {
        return self::where('status', self::DA_DUYET)
            ->with([
                'export_products' => function($q) {
                    $q->with([
                        'product' => function($q) {
                            $q->select(['id', 'name', 'code']);
                        }
                    ]);
                }
            ])
            ->orderBy('approved_time', 'ASC')
            ->get();
    }

	public static function getDataForWarehouseExport($id) {
        return self::where('id', $id)
            ->with([
                'export_products' => function($q) {
                    $q->with([
                        'product' => function($q) {
                            $q->select(['id', 'name', 'code']);
                        }
                    ]);
                },
                'customer'
            ])
            ->first();
    }

    public static function getDataForSelect() {
        return self::select(['id', 'code'])
            ->orderBy('bill_date', 'desc')
            ->get();
    }

    public static function getReportByDates($from_date, $to_date)
    {
        $rsl = self::query()
            ->selectRaw(DB::raw('sum(cost_after_sale) as total_cost'))
            ->whereBetween('bill_date', [$from_date, $to_date])
            ->where('g7_id', Auth::user()->g7_id)
            ->first();
        return $rsl->total_cost;
    }

	public static function promoReportQuery($request) {
		$result = self::from('bills as b')
			->join('promo_campaigns as pc', 'b.promo_id', '=', 'pc.id')
			->join('g7_infos as g', 'b.g7_id', '=', 'g.id')
			->whereIn('b.status', [self::DA_DUYET, self::DA_CHOT]);

		if (Auth::user()->type == User::G7) {
			$result = $result->where('b.g7_id', Auth::user()->g7_id);
		}

		if (!empty($request->promo_id)) {
			$result = $result->where('b.promo_id', $request->promo_id);
		}

		if (!empty($request->customer_id)) {
			$result = $result->where('b.customer_id', $request->customer_id);
		}

		if (!empty($request->g7_id)) {
			$result = $result->where('b.g7_id', $request->g7_id);
		}

		if (!empty($request->from_date)) {
			$result = $result->where('b.bill_date', '>=', $request->from_date);
		}

		if (!empty($request->to_date)) {
			$result = $result->where('b.bill_date', '<', addDay($request->to_date));
		}

		return $result;
	}

	public static function promoReportSearchData($request) {
		$result = self::promoReportQuery($request);
		$result = $result->select([
			'b.*', 'pc.name as promo_name', 'pc.code as promo_code', 'g.name as g7'
		]);
		return $result;
	}

	public static function promoReportSummary($request) {
		$result = self::promoReportQuery($request);
		$result = $result->select([
			DB::raw('SUM(b.cost_after_sale) as cost_after_sale'),
			DB::raw('SUM(b.promo_value) as promo_value'),
		])->first();
		return $result;
	}

	public static function promoProductReportSearchData($request) {
		$result = self::promoReportQuery($request);
		$result = $result->select([
			'b.*', 'pc.name as promo_name', 'pc.code as promo_code', 'g.name as g7'
		])
		->with([
			'promo_products'
		]);
		return $result;
	}

	public static function promoProductReportSummary($request) {
		$result = self::promoReportQuery($request);
		$result = $result->join('bill_promo_products as pp', 'pp.parent_id', '=', 'b.id')
			->select([
				DB::raw('SUM(pp.qty) as qty'),
			])->first();
		return $result;
	}

	public static function customerSaleReportQuery($request) {
		$result = self::from('bills as b')
			->join('customers as c', 'b.customer_id', '=', 'c.id')
			->leftJoin('provinces as p', 'c.province_id', '=', 'p.id')
			->leftJoin('districts as d', 'c.district_id', '=', 'd.id')
			->leftJoin('wards as w', 'c.ward_id', '=', 'w.id')
			->whereIn('b.status', [self::DA_DUYET, self::DA_CHOT]);

		if (Auth::user()->type == User::G7) {
			$result = $result->where('b.g7_id', Auth::user()->g7_id);
		}

		if (!empty($request->province_id)) {
			$result = $result->where('c.province_id', $request->province_id);
		}

		if (!empty($request->customer_id)) {
			$result = $result->where('b.customer_id', $request->customer_id);
		}

		if (!empty($request->g7_id)) {
			$result = $result->where('b.g7_id', $request->g7_id);
		}

		if (!empty($request->from_date)) {
			$result = $result->where('b.bill_date', '>=', $request->from_date);
		}

		if (!empty($request->to_date)) {
			$result = $result->where('b.bill_date', '<', addDay($request->to_date));
		}

		return $result;
	}

	public static function customerSaleReportSearchData($request) {
		$result = self::customerSaleReportQuery($request);
		$result = $result->select([
			'c.name', 'c.code', 'c.mobile', 'c.adress as address', 'p.name as province', 'd.name as district',
			'w.name as ward', DB::raw('SUM(b.cost_after_vat) as total_cost'), DB::raw('SUM(b.payed_value) as payed'),
			DB::raw('SUM(b.cost_after_vat - b.payed_value) as remain')
		])
		->groupBy([
			'c.name', 'c.code', 'c.mobile', 'c.adress', 'p.name', 'd.name', 'w.name'
		]);
		return $result;
	}

	public static function customerSaleReportSummary($request) {
		$result = self::customerSaleReportQuery($request);
		$result = $result->select([
			DB::raw('SUM(b.cost_after_vat) as total_cost'), DB::raw('SUM(b.payed_value) as payed'),
			DB::raw('SUM(b.cost_after_vat - b.payed_value) as remain')
		])->first();
		return $result;
	}
}
