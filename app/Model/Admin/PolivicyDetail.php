<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class PolivicyDetail extends BaseModel
{
    protected $table = 'polivicy_details';

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public static function getDataForEdit($id)
    {
        return self::query()->where('id', $id)->firstOrFail();
    }
}
