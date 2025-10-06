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
use \Carbon\Carbon;

class ActivityLog extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public static function getForDisplay() {
        $result = self::query()
			->with([
				'user' => function($q) {
					$q->select([
						'id', 'name'
					]);
				}
			]);

		$result = $result->where('user_id', auth()->user()->id);

		$result = $result->where('time', '>', date('Y-m-d'))
			->orderBy('time', 'DESC')
			->limit(5)
            ->get();

		return $result;
    }

    public static function createRecord($content, $link) {
		$obj = new ActivityLog();
		$obj->link = $link;
		$obj->content = $content;
		$obj->user_id = auth()->user()->id;
		$obj->time = Carbon::now();
		$obj->save();
    }

}
