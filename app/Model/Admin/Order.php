<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\Customer;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['id', 'customer_name', 'customer_address', 'customer_id',
        'customer_email', 'customer_phone', 'customer_required', 'payment_method', 'created_at', 'updated_at', 'code', 'discount_code', 'discount_value', 'total_before_discount', 'total_after_discount'];

    protected $appends = ['total_price'];

    public const MOI = 10;
    public const DUYET = 20;
    public const THANH_CONG = 30;
    public const HUY = 40;

    public const PAYMENT_METHODS = [1=> 'Thanh toán khi nhận hàng - COD', 0 => 'Chuyển khoản ngân hàng'];

    public const STATUSES = [
        [
            'id' => self::MOI,
            'name' => 'Mới',
            'type' => 'danger'
        ],
        [
            'id' => self::DUYET,
            'name' => 'Đã duyệt',
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
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getTotalPriceAttribute()
    {
        if ($this->total_after_discount > 0) {
            return $this->total_after_discount;
        }

        return $this->details->sum(function ($detail) {
            return $detail->price * $detail->qty;
        }) - $this->discount_value;
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

    public static function formatFromNumber($n, $minDigits = 6)
    {
        $digits = max($minDigits, strlen((string)$n));
        return 'DH-' . str_pad($n, $digits, '0', STR_PAD_LEFT);
    }
}
