<?php

namespace App\Model\G7;
use Auth;
use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\User;
use DB;

class FinalWarehouseAdjust extends BaseModel
{
    protected $table = 'final_warehouse_adjusts';

    public CONST STATUSES = [

    ];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'final_warehouse_adjust_id','id');
    }

    public function details()
    {
        return $this->hasMany(FinalWarehouseAdjustDetail::class, 'parent_id','id');
    }

    public function canView() {
        return Auth::user()->g7_id == $this->g7_id;
    }

    public static function searchByFilter($request) {
        $result = self::query();

        if (Auth::user()->type == User::SUPER_ADMIN || Auth::user()->type == User::QUAN_TRI_VIEN) {

        } else if (Auth::user()->type == User::NHOM_G7) {

        } else $result = $result->where('g7_id', Auth::user()->g7_id);

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if (!empty($request->created_by)) {
            $result = $result->where('created_by', $request->created_by);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }

    public function syncBills($bills) {
        Bill::where('final_warehouse_adjust_id', $this->id)->update([
            'status' => Bill::DA_DUYET,
            'final_warehouse_adjust_id' => null
        ]);
        FinalWarehouseAdjustDetail::where('parent_id', $this->id)->delete();
        if ($bills) {
            $object = [];
            foreach ($bills as $b) {
                $bill = Bill::find($b['id']);
                $bill->status = Bill::DA_CHOT;
                $bill->final_warehouse_adjust_id = $this->id;
                $bill->save();

                foreach ($b['export_products'] as $p) {
                    $export_product = BillExportProduct::where('id', $p['id'])
                        ->where('parent_id', $b['id'])
                        ->first();
                    $diff = $p['real_qty'] - $export_product->qty;
                    if (!isset($object[$export_product->product_id])) $object[$export_product->product_id] = 0;
                    $object[$export_product->product_id] += $diff;

                    $export_product->real_qty = $p['real_qty'];
                    $export_product->note = setDefault($p, 'note');
                    $export_product->save();
                }
            }
            foreach ($object as $key => $value) {
                if ($value == 0) continue;
                $item = new FinalWarehouseAdjustDetail();
                $item->parent_id = $this->id;
                $item->product_id = $key;
                $item->change = $value;
                $item->save();
            }
        }
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'bills' => function($q) {
                    $q->with([
                        'export_products' => function($q) {
                            $q->with([
                                'product' => function($q) {
                                    $q->select(['id', 'code', 'name']);
                                }
                            ]);
                        }
                    ]);
                },
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "CVT-".generateCode(5, $this->id);
        $this->save();
    }

    public function updateWarehouse() {
        foreach ($this->details as $p) {
            $stock = Stock::where('product_id', $p->product_id)->where('g7_id', $this->g7_id)->first();
            if (!$stock) {
                $stock = new Stock();
                $stock->g7_id = $this->g7_id;
                $stock->product_id = $p->product_id;
                $stock->qty = 0;
                $stock->save();
            }

            $log = new StockLog();
            $log->stock_id = $stock->id;
            $log->final_warehouse_adjust_detail_id = $p->id;
            $log->qty_before = $stock->qty;
            $log->change = -$p->change;
            $log->qty_after = $stock->qty - $p->change;
            $log->save();

            $stock->qty -= $p->change;
            $stock->save();
        }
    }

}
