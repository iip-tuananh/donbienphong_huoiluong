<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\History;

class Version extends Model
{
    public function histories () {
        return $this->hasMany(History::class ,'version_id','id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id' );
    }
}
