<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['domain_id', 'title', 'type', 'url', 'order', 'hall_id'];

    public function domain() {
        return $this->hasOne('App\Models\Domain', 'id', 'domain_id');
    }

    public function upload() {
        return $this->belongsTo('App\Models\Upload', 'id', 'page_id');
    }

    public function hall()
    {
        return $this->hasOne('App\Models\Hall', 'id', 'hall_id');
    }
}
