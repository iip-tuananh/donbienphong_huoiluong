<?php

namespace App\Model\Common;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Model\Common
 */
class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'model_id',
        'model_type',
        'custom_field'
    ];

    public function model()
    {
        return $this->morphTo();
    }

	public function removeFromDB() {
        $this->delete();
    }
}
