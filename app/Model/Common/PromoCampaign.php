<?php

namespace App\Model\Common;
use Auth;
use App\Model\BaseModel;
use App\Model\Product;
use App\Model\Uptek\G7Info;
use Illuminate\Database\Eloquent\Model;
use DB;

class PromoCampaign extends BaseModel
{
    protected $table = 'promo_campaigns';

    public CONST ACTIVE = 1;
    public CONST LOCK = 2;

	public CONST GIA_TRI = 1;
	public CONST SAN_PHAM = 2;
	public CONST MUA_HANG_TANG_HANG = 3;

    public CONST STATUSES = [
        [
            'id' => self::ACTIVE,
            'name' => 'Hoạt động',
            'type' => 'success'
        ],
        [
            'id' => self::LOCK,
            'name' => 'Khóa',
            'type' => 'danger'
        ],
    ];

    public function checkpoints()
    {
        return $this->hasMany(PromoCampaignCheckpoint::class, 'parent_id','id');
    }

	public function g7s()
    {
        return $this->belongsToMany(G7Info::class, G7HasPromoCampaign::class, 'promo_campaign_id', 'g7_id');
    }

	public function product ()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }

	public function canEdit() {
		return in_array(Auth::user()->type, [User::SUPER_ADMIN, User::QUAN_TRI_VIEN]) || Auth::user()->g7_id == $this->user_create->g7_id;
	}

    public function canLock() {
        return $this->canEdit() && $this->status == self::ACTIVE;
    }

	public function canUnLock() {
        return $this->canEdit() && $this->status == self::LOCK;
    }

    public function canUse() {
        $result = $this->status == self::ACTIVE &&
			(!$this->start_date || $this->start_date <= date('Y-m-d')) &&
			(!$this->end_date || $this->end_date > date('Y-m-d'));
		if (!$this->for_all) {
			$valid = G7HasPromoCampaign::where('promo_campaign_id', $this->id)->where('g7_id', auth()->user()->g7_id)->first();
			$result = $result && !!$valid;
		}
		return $result;
    }

    public static function searchByFilter($request) {
        $result = self::query();

		if ($request->type == 'use') {
			$result = $result->where('status', self::ACTIVE)
				->where(function($q) {
					$q->where('start_date', null)->orWhere('start_date', '<=', date('Y-m-d'));
				})
				->where(function($q) {
					$q->where('end_date', null)->orWhere('end_date', '>', date('Y-m-d'));
				})
				->where(function($q) {
					$ids = G7HasPromoCampaign::where('g7_id', auth()->user()->g7_id)->pluck('promo_campaign_id')->toArray();
					$q->where('for_all', true)->orWhereIn('id', $ids);
				});
		}

        if (!empty($request->code)) {
            $result = $result->where('code', 'like', '%'.$request->code.'%');
        }

		if (!empty($request->name)) {
            $result = $result->where('name', 'like', '%'.$request->name.'%');
        }

        if (!empty($request->updated_by)) {
            $result = $result->where('updated_by', $request->updated_by);
        }

        if (!empty($request->status)) {
            $result = $result->where('status', $request->status);
        }

        if (empty($request->order)) {
            $result = $result->orderBy('created_at','desc')->get();
        }

        return $result;
    }

    public function syncCheckPoints($list) {
		PromoCampaignCheckpointProduct::where('campaign_id', $this->id)->delete();
        PromoCampaignCheckpoint::where('parent_id', $this->id)->delete();
        if ($list) {
            foreach ($list as $index => $l) {
				$item = new PromoCampaignCheckpoint();
				$item->parent_id = $this->id;
				$item->from = $l['from'];
				$item->to = setDefault($l, 'to');
				if ($this->type == self::GIA_TRI) {
					$item->type = $l['type'];
					$item->value = ($l['type'] == 1 && $l['value'] > 100) ? 100 : $l['value'];
				}
				$item->save();

				if ($this->type == self::SAN_PHAM || $this->type = self::MUA_HANG_TANG_HANG) {
					$item->syncProducts($l['products']);
				}
            }
        }
    }

    public static function getDataForEdit($id) {
		return self::where('id', $id)
		->with([
			'checkpoints' => function($q) {
				$q->with([
					'products' => function($q) {
						$q->with([
							'product'
						]);
					}
				]);
			},
			'product',
			'g7s',
		])
		->firstOrFail();
    }

    public static function getDataForShow($id) {
        return self::where('id', $id)
            ->with([
                'checkpoints' => function($q) {
					$q->with([
						'products' => function($q) {
							$q->with([
								'product'
							]);
						}
					]);
				},
				'product',
                'g7s',
            ])
            ->firstOrFail();
    }

    public function generateCode() {
        $this->code = "CTKM-".generateCode(5, $this->id);
        $this->save();
    }

    public static function getDataForUse($id) {
        return self::where('id', $id)
            ->with([
                'checkpoints' => function($q) {
					$q->with([
						'products' => function($q) {
							$q->with([
								'product'
							]);
						}
					]);
				},
            ])
            ->firstOrFail();
    }

	public static function getForSelect() {
		$result = self::query();
		if (Auth::user()->g7_id) {
			$ids = G7HasPromoCampaign::where('g7_id', auth()->user()->g7_id)->pluck('promo_campaign_id')->toArray();
			$result = $result->where('for_all', true)->orWhereIn('id', $ids);
		}
		return $result->get();
	}
}
