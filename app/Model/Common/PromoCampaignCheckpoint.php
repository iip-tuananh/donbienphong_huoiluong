<?php

namespace App\Model\Common;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class PromoCampaignCheckpoint extends Model
{
    public function products()
    {
        return $this->hasMany(PromoCampaignCheckpointProduct::class, 'parent_id','id');
    }

	public function syncProducts($list) {
        if ($list) {
            foreach ($list as $index => $l) {
				$item = new PromoCampaignCheckpointProduct();
				$item->parent_id = $this->id;
				$item->campaign_id = $this->parent_id;
				$item->product_id = $l['product_id'];
				$item->qty = $l['qty'];
				$item->save();
            }
        }
    }
}
