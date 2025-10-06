<?php

namespace App\Model\Admin;

use App\Model\BaseModel;

class TourCategorySpecial extends BaseModel
{
    protected $table = 'tour_category_special';
    protected $fillable = ['tour_id', 'category_special_id'];

}
