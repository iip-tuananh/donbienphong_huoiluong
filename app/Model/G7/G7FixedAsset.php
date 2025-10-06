<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use App\Model\Uptek\FixedAsset;
use Carbon\Carbon;
use Carbon\Traits\Creator;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Auth;
use App\Model\G7\G7FixedAssetImportDetail;

class G7FixedAsset extends BaseModel
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

    public function canDelete() {

        if(Auth::user()->g7_id == $this->g7_id ) {
            if($this->g7FixedAssetImportDetail->count() > 0) {
                return false;
            } else {
                return true;
            }
        }
        return false;

    }

    public function canView()
    {
        return true;
    }

    public function canEdit()
    {
        return Auth::user()->g7_id == $this->g7_id;
    }

    public function image()
    {
        return $this->morphOne(File::class, 'model');
    }

    public function g7FixedAssetImportDetail()
    {
        return $this->hasMany(G7FixedAssetImportDetail::class,'asset_id','id');
    }

    public function root()
    {
        return $this->belongsTo(FixedAsset::class, 'root_id', 'id');
    }

    public static function searchByFilter($request) {
        $result = self::where('g7_id', Auth::user()->g7_id)
            ->with([
                'image'
            ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::select(['id', 'name'])
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'root',
                'image'
            ])
            ->firstOrFail();
    }

	public static function getData($id) {
        return self::where('id', $id)
            ->with([
                'image'
            ])
            ->firstOrFail();
    }

    /**
     * @param $price
     * @param $depreciation_period
     * @param $import_date
     * @param int $qty
     * @return float|int
     */
    public static function getDepreciated($price, $depreciation_period, $import_date, $qty = 1, $from_date = null, $to_date = null)
    {
        $total_price = $price * $qty;
        $now = Carbon::now();
        $import_date = new Carbon($import_date);

        if ($from_date && $to_date) {
            if ($from_date > $to_date) {
                return 0;
            } else {
                $depreciation_date = (new Carbon($import_date))->addMonths($depreciation_period);
                if ($from_date > $depreciation_date) {
                    return 0;
                } elseif ($to_date < $import_date) {
                    return 0;
                } else {
                    if ($import_date < $from_date) {
                        $import_date = new Carbon($from_date);
                    }
                    if ($now > $to_date) {
                        $now = new Carbon($to_date);
                    }
                }
            }
        }

        $depreciated_each_month = $price / $depreciation_period;
        $depreciated_each_day_of_imported_month = $depreciated_each_month / $import_date->daysInMonth;
        $depreciated_each_day_of_this_month = $depreciated_each_month / $now->daysInMonth;

        $days_used_of_import_month = $import_date->daysInMonth - $import_date->day;
        $month_used = $now->diffInMonths($import_date) - 1;
        $month_used = $month_used > 0 ?: 0;

        $depreciated = $qty * ($depreciated_each_day_of_imported_month * $days_used_of_import_month // khấu hao theo ngày sử dụng
            + $month_used * $depreciated_each_month // khấu hao theo tháng sử dụng
            + $depreciated_each_day_of_this_month * $now->day); // khấu hao theo ngày sử dụng
        return round($depreciated <= $total_price ? $depreciated : $total_price);
    }
}
