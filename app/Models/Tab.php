<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tab extends Model
{
    protected $fillable = ['index', 'header_id', 'title'];

    public function media()
    {
        return $this->hasMany('App\Models\Upload', 'tab_id', 'id');
    }
}
