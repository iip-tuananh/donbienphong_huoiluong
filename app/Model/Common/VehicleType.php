<?php
namespace App\Model\Common;
use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;
use Auth;

class VehicleType extends BaseModel
{

    public CONST HOAT_DONG = 1;
    public CONST DA_KHOA = 0;

    public CONST STATUSES = [
        [
            'id' => self::HOAT_DONG,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => self::DA_KHOA,
            'name' => 'Đã khóa',
            'type' => 'danger'
        ]
    ];

    public function canDelete() {
        return true;
    }

    public function manufact()
    {
        return $this->belongsTo('App\Model\Common\VehicleManufact','vehicle_manufact_id','id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Common\VehicleCategory','vehicle_category_id','id');
    }

    public static function searchByFilter($request) {
        $result = self::with(['manufact']);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->manufacts)) {
            $result = $result->where('vehicle_manufact_id',$request->manufacts);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc');
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function getData($id) {
        return self::where('id',$id)
            ->with('manufact')
            ->first();
    }
}
