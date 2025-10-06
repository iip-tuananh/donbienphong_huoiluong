<?php

namespace App\Model\Common;
use Illuminate\Database\Eloquent\Model;
use App\Model\BaseModel;
use Auth;

class CustomerGroup extends BaseModel
{
    // Status = 1 =>> Hoạt động
    // Status = 0 =>> Khóa

    public function canDelete() {
        return true;
    }

    public static function searchByFilter($request) {
        $result = self::with([]);

        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
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
}
