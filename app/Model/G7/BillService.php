<?php

namespace App\Model\G7;

use App\Model\G7\G7Product;
use Illuminate\Database\Eloquent\Model;
use App\Model\Uptek\ServiceVehicleCategoryGroup;
use App\Model\Uptek\Service;

class BillService extends Model
{

    public function group ()
    {
        return $this->belongsTo(ServiceVehicleCategoryGroup::class, 'group_id','id');
    }

    public function service ()
    {
        return $this->belongsTo(Service::class, 'service_id','id');
    }
}
