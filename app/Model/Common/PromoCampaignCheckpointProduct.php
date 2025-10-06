<?php

namespace App\Model\Common;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class PromoCampaignCheckpointProduct extends Model
{
	public function product ()
    {
        return $this->belongsTo(Product::class, 'product_id','id');
    }
}
