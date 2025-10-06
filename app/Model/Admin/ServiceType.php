<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class ServiceType extends BaseModel
{
    protected $table = 'service_types';

    use Sluggable;
    use SluggableScopeHelpers;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public CONST TRAI_NGHIEM = 1;
    public CONST UU_DAI = 2;
    public CONST GOI_NGHI_DUONG = 3;

    public CONST HOAT_DONG = 1;
    public CONST HUY = 0;

    public CONST STATUSES = [
        [
            'id' => self::HOAT_DONG,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => self::HUY,
            'name' => 'Hủy',
            'type' => 'danger'
        ],
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'service_type_id', 'id');
    }

    public static function searchByFilter($request)
    {
        $objects = self::query();
        if (!empty($request->type)) {
            $objects->where('type', $request->type);
        }
        if (!empty($request->name)) {
            $objects->where('name', 'like', '%' . $request->name . '%');
        }
        if (!empty($request->status)) {
            $objects->where('status', $request->status);
        }
        return $objects;
    }

    public function canEdit()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public static function getForSelect($type)
    {
        return self::where('status', self::HOAT_DONG)->where('type', $type)->get();
    }
}
