<?php

namespace App\Model\G7;

use App\Model\Common\VehicleCategory;
use Illuminate\Database\Eloquent\Model;

class G7ServiceVehicleCategoryGroup extends Model
{
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(G7ServiceVehicleCategoryGroupProduct::class, 'parent_id','id');
    }

    public function service ()
    {
        return $this->belongsTo(G7Service::class, 'g7_service_id','id');
    }

    public function syncProducts($items) {
        if ($items) {
            foreach ($items as $i) {
                $item = new G7ServiceVehicleCategoryGroupProduct();
                $item->parent_id = $this->id;
                $item->g7_service_id = $this->g7_service_id;
                $item->g7_product_id = $i['g7_product_id'];
                $item->qty = $i['qty'];
                $item->save();
            }
        }
    }
}
