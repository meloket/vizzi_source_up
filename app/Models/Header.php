<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    public function tab()
    {
        return $this->hasMany('App\Models\Tab', 'header_id', 'id');
    }
}
