<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    public function types () {
        return $this->hasMany('App\Model\Common\PermissionHasType', 'permission_id', 'id');
    }

    public static function getAll() {
        return self::all();
    }

    public static function createRecord($data, $types) {
        $object = self::create($data);
//        foreach($types as $type) {
//            PermissionHasType::create(['permission_id' => $object->id, 'type' => $type]);
//        }
    }
}
