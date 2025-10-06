<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use App\Model\Admin\Product;
use App\Model\Admin\DesignDetail;

class DesignOrder extends Model
{
    protected $table = 'design_orders';
    protected $fillable = [];

    public const MOI = 0;
    public const CHO_XU_LY = 1;
    public const DANG_XU_LY = 2;
    public const DA_HOAN_THANH = 3;
    public const HUY = 4;

    public const STATUSES = [
        [
            'id' => self::MOI,
            'name' => 'Mới',
            'type' => 'danger'
        ],
        [
            'id' => self::CHO_XU_LY,
            'name' => 'Chờ xử lý',
            'type' => 'warning'
        ],
         [
            'id' => self::DANG_XU_LY,
            'name' => 'Đang xử lý',
            'type' => 'info'
        ],
        [
            'id' => self::DA_HOAN_THANH,
            'name' => 'Đã hoàn thành',
            'type' => 'success'
        ],
         [
            'id' => self::HUY,
            'name' => 'Hủy',
            'type' => 'danger'
        ],
    ];

    public function details()
    {
        return $this->hasMany(DesignDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image_front()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_front');
    }

    public function image_back()
    {
        return $this->morphOne(File::class, 'model')->where('custom_field', 'image_back');
    }

    public static function searchByFilter($request)
    {
        $result = self::query();

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%' . $request->code . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }
}
