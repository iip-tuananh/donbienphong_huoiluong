<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $fillable = ['name', 'code', 'value', 'quantity', 'limit_bill_value', 'limit_product_qty', 'limit_user_qty', 'from_date', 'to_date', 'description', 'content', 'status'];

    public static function searchByFilter($request)
    {
        $result = self::query();

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }
        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%' . $request->code . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public static function getDataForEdit($id)
    {
        return self::where('id', $id)
            ->firstOrFail();
    }
}
