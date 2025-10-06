<?php

namespace App\Model\G7;

use App\Model\G7\G7Product;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    public function stock ()
    {
        return $this->belongsTo(Stock::class, 'stock_id','id');
    }

    public function warehouse_import_detail ()
    {
        return $this->belongsTo(WareHouseImportDetail::class, 'warehouse_import_detail_id','id');
    }

    public function bill_export_product ()
    {
        return $this->belongsTo(BillExportProduct::class, 'bill_export_product_id','id');
    }
}
