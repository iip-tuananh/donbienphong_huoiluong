<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use App\Model\Common\File;
use Vanthao03596\HCVN\Models\Province;

class Store extends BaseModel
{
    protected $table = 'stores';

    public static function searchByFilter($request)
    {
        $result = self::with([
            'province',
        ]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%' . $request->name . '%');
        }

        $result = $result->orderBy('created_at', 'desc')->get();
        return $result;
    }

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id','id');
    }

    public static function getDataForEdit($id)
    {
        return self::with('province')->where('id', $id)
            ->firstOrFail();
    }

    public function canDelete()
    {
        return true;
    }
}
