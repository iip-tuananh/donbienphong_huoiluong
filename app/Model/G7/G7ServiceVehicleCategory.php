<?php

namespace App\Model\G7;

use App\Model\Common\VehicleCategory;
use Illuminate\Database\Eloquent\Model;

class G7ServiceVehicleCategory extends Model
{
    protected $table = 'g7_service_vehicle_categories';

    public function groups()
    {
        return $this->hasMany(G7ServiceVehicleCategoryGroup::class, 'parent_id', 'id');
    }

    public function syncGroups($items) {
        if ($items) {
            foreach ($items as $i) {
                $item = new G7ServiceVehicleCategoryGroup();
                $item->parent_id = $this->id;
                $item->g7_service_id = $this->g7_service_id;
                $item->name = $i['name'];
                $item->service_price = $i['service_price'];
                $item->save();

                $item->syncProducts($i['products']);
            }
        }
    }
}
