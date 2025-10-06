<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class TermDetail extends BaseModel
{
    protected $table = 'term_details';

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public static function getDataForEdit($id)
    {
        return self::query()->where('id', $id)->firstOrFail();
    }
}
