<?php

namespace App\Model\G7;

use Illuminate\Database\Eloquent\Model;
use App\Model\Product;

class WareHouseImportDetail extends Model
{

    protected $table = "ware_house_import_detail";

    public function canDelete() {
        return true;
    }

    public function wareHouseImport()
    {
        return $this->belongsTo(WareHouseImport::class,'ware_house_import_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public static function searchByFilter($request) {
        $result = self::with(['wareHouseImport']);

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }
}
