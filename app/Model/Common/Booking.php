<?php

namespace App\Model\Common;
use Auth;
use App\Model\BaseModel;
use App\Model\Common\Customer;
use App\Model\Common\User;
use Illuminate\Database\Eloquent\Model;
use App\Model\Common\File;
use DB;
use App\Model\Common\Notification;
use App\Model\Uptek\G7Info;

class Booking extends BaseModel
{
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

    public function g7Info()
    {
        return $this->belongsTo(G7Info::class,'g7_id','id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }

    public static function searchByFilter($request) {

        $result = self::with([
            'customer',
            'g7Info'
        ]);

        if(Auth::user()->type == 3) {
            $result = $result->where('g7_id', Auth::user()->g7_id);
        }
        if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->mobile)) {
            $result = $result->where('mobile', 'like', '%'.$request->mobile.'%');
        }
        if(Auth::user()->type == 1) {
            if (!empty($request->g7_id)) {
                $result = $result->where('g7_id', $request->g7_id);
            }
        }

        if ($request->status === 0 || $request->status === '0' || !empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        $result = $result->orderBy('created_at','desc')->get();
        return $result;
    }

    public static function getForSelect() {
        return self::where('status', 1)
            ->select(['id', 'name'])
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function getDataForEdit($id) {
        return self::where('id', $id)
            ->with([
                'customer',
                'g7Info',
                'user_create'
            ])
            ->firstOrFail();
    }

    public static function getDataForShow($id)
    {
        return self::where('id', $id)
        ->with([
            'customer',
            'g7Info',
            'user_create'
        ])
            ->firstOrFail();
    }

    public function canEdit()
    {
        return $this->created_by == Auth::user()->id;
    }

    public function canView()
    {
        return $this->status == 1 || $this->created_by == Auth::user()->id;
    }

    public function canDelete ()
    {
        return Auth::user()->id == $this->created_by;
    }

    public function send()
    {
        foreach($this->g7Info->users as $user) {
            $notification = new Notification();
            $notification->url = route("Post.show", $this->id, false);
            $notification->content = "Có đặt lịch mới từ hệ thống G7 Autocare";
            $notification->status = 0;
            $notification->receiver_id = $user->id;
            $notification->created_by = Auth::user()->id;
            $notification->save();

            $notification->send();
        }
    }

}
