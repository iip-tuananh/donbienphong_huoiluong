<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use Response;
use Auth;
use stdClass;
use App\Model\Common\Notification;
use App\Model\Common\User;
use DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;

class NotificationsController extends Controller
{
    use ResponseTrait;

    //Lấy danh sách thông báo của user
    public function searchData(Request $request)
    {
        $skip = (int) $request->get("skip");
        $notifications = Notification::leftJoin('users', 'notifications.created_by', '=', 'users.id')
            ->leftJoin('files as f', function($join) {
                $join->on('f.model_id', '=', 'users.id')->on('f.model_type', '=', DB::raw("'".User::class."'"));
            })
            ->where('receiver_id', Auth::user()->id)
            ->select(['notifications.*', 'users.name as sender_name', DB::raw("f.path as sender_avatar")])
            ->orderBy('created_at', 'DESC')
            ->skip($skip)
            ->take(10)
            ->get();
        $unread = Notification::where([
                ["receiver_id", Auth::user()->id],
                ["status", '!=', 1]
            ])
            ->count();
        $data = [
            "notifications" => $notifications,
            "unread" => $unread
        ];
        return $this->responseSuccess('', $data);
    }

    public function read(Request $request, $id) {
        Notification::where('id', $id)
            ->where('receiver_id', Auth::user()->id)
            ->update(['status' => 1]);
        return $this->responseSuccess();
    }

    public function index() {
        return view('common.notifications.index');
    }
}
