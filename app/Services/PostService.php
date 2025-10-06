<?php

namespace App\Services;

use App\Model\Admin\Category;
use App\Model\Admin\OrderDetail;

class PostService {
    public static function canAccessPost($post) {
        // post miễn phí type = 1
        if($post->type == 1) {
            $access = true;
        } else {
            if(! auth('customer')->check()) {
                $access = false;
            } else {
                $exists = OrderDetail::query()->where('customer_id', auth('customer')->id())
                    ->where('post_id', $post->id)
                    ->where('status',2)
                    ->exists();
                if($exists) { $access = true; } else { $access = false; }
            }
        }

        return $access;
    }


    public static function attachAccess($posts, $customerId)
    {
        if (!$customerId) {
            return $posts->each(function($p){ $p->access = ($p->type == 1); });
        }

        $ids = $posts->pluck('id')->all();
        $paidIds = OrderDetail::query()
            ->where('customer_id',$customerId)
            ->whereIn('post_id',$ids)
            ->where('status',2)
            ->pluck('post_id')
            ->all();

        $paidSet = array_flip($paidIds);

        return $posts->each(function($p) use ($paidSet){
            $p->access = ($p->type==1) || isset($paidSet[$p->id]);
        });
    }

}
