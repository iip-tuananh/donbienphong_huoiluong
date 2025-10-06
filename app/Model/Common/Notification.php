<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\User;
use Illuminate\Support\Facades\Redis;

class Notification extends Model
{
    public function sender()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function send() {
        Redis::publish("user_notification_".$this->receiver_id, messageFromNotification($this));
    }
}
