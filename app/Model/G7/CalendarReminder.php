<?php

namespace App\Model\G7;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use Auth;

class CalendarReminder extends BaseModel
{
    public CONST DANG_CHO = 1;
    public CONST DA_XU_LY = 2;

    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Đang chờ',
            'type' => 'danger'
        ],
        [
            'id' => 2,
            'name' => 'Đã xử lý',
            'type' => 'success'
        ]
    ];

    // Loại nhắc lịch

    public CONST TYPES = [
        [
            'id' => 1,
            'name' => 'Nhắc lịch bảo dưỡng',
        ],
        [
            'id' => 2,
            'name' => 'Nhắc lịch đăng kiếm',
        ],
        [
            'id' => 3,
            'name' => 'Nhắc lịch bảo hiểm',
        ]
    ];

    public static function getReminderType($type_id)
    {
        foreach(self::TYPES as $type) {
            if($type['id'] == $type_id) {
                return $type['name'];
            }
        }
    }

    public function getReminderId($type_id)
    {
        foreach(self::TYPES as $type) {
            $type_ids[] = $type->id;
        }
        return $type_ids;
    }

    public function canDelete() {
        return Auth::user()->id == $this->created_by || Auth::user()->type == 3 && Auth::user()->g7_id == $this->g7_id;
    }

    public function canUpdate() {
        if($this->status == 2) {
            return false;
        } else {
            return Auth::user()->id == $this->created_by || Auth::user()->type == 3 && Auth::user()->g7_id == $this->g7_id;
        }
        return false;
    }

    public function customer()
    {
        return $this->belongsTo('App\Model\Common\Customer','customer_id','id');
    }

    public function car()
    {
        return $this->belongsTo('App\Model\Common\Car','car_id','id');
    }

    public static function searchByFilter($request) {
        $result = self::where('g7_id', Auth::user()->g7_id)
            ->with([
                'customer',
                'car' => function($q) {
                    $q->with([
                        'licensePlate'
                    ]);
                },
            ]);
        if($request->start_date) {
            $result = $result->whereDate('reminder_date','>=',$request->start_date);
        }

        if($request->type_id) {
            $result = $result->where('reminder_type', $request->type_id);
        }

        if($request->customer_id) {
            $result = $result->where('customer_id', $request->customer_id);
        }

        if($request->type_id) {
            $result = $result->where('reminder_type', $request->type_id);
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

	public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'customer',
                'car'
            ])
            ->firstOrFail();
    }
}
