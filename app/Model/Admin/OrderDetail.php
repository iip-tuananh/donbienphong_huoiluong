<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    public CONST STATUSES = [
        [
            'id' => 1,
            'name' => 'Hủy',
            'type' => 'danger'
        ],
        [
            'id' => 2,
            'name' => 'Kích hoạt',
            'type' => 'success'
        ]
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
