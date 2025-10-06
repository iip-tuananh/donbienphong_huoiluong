<?php

namespace App\Model\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;

class ProductRate extends Model
{
    const STATUS_WAITING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_UNAPPROVED = 0;

    const STATUSES = [
        ['id' => self::STATUS_WAITING, 'name' => 'Chờ duyệt', 'type' => 'warning'],
        ['id' => self::STATUS_APPROVED, 'name' => 'Đã duyệt', 'type' => 'success'],
        ['id' => self::STATUS_UNAPPROVED, 'name' => 'Không duyệt', 'type' => 'danger'],
    ];

    protected $table = 'product_rates';

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'phone',
        'rating',
        'title',
        'desc',
    ];

    public function images()
    {
        return $this->morphMany(File::class, 'model');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function removeFromDB() {
        foreach ($this->images as $image) $image->removeFromDB();
        $this->delete();
    }

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
        return self::with(['product', 'images'])->where('id', $id)
            ->firstOrFail();
    }
}
