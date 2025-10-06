<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Auth;
use \Carbon\Carbon;

class BaseModel extends Model
{
    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            if (Auth::user() && !$model->created_by) $model->created_by = Auth::user()->id;
            if (Auth::user()) $model->updated_by = Auth::user()->id;
        });

        self::saving(function($model){
            if (Auth::user()) $model->updated_by = Auth::user()->id;
        });
    }


    public function user_create ()
    {
        return $this->belongsTo('App\Model\Common\User', 'created_by','id');
    }

    public function user_update ()
    {
        return $this->belongsTo('App\Model\Common\User', 'updated_by','id');
    }

    public function getCreatedFormatAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getLastUpdatedAttribute($value)
    {
        return $this->updated_at ? Carbon::parse($this->updated_at)->format("d/m/Y") : Carbon::parse($this->created_at)->format("d/m/Y");
    }

    public static function fillReport($template, $data) {
        foreach ($data as $key => $value) {
            $template = preg_replace("/\{\{".$key."\}\}/", $value, $template);
        }
        return $template;
    }

    public static function clearNull($template) {
        $template = preg_replace("/\{\{.*?\}\}/", "", $template);
        return $template;
    }
}
