<?php

namespace App\Model\Admin;

use App\Model\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use DB;

class Term extends BaseModel
{
    protected $table = 'terms';

    public function canEdit()
    {
        return Auth::user()->id = $this->created_by;
    }

    public function details() {
        return $this->hasMany(TermDetail::class, 'term_id');
    }


    public static function getDataForEdit($id)
    {
        return self::with('details')->where('id', $id)->firstOrFail();
    }
}
