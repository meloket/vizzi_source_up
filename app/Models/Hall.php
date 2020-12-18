<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = [
        'domain_id', 'user_id', 'type', 'name', 'status', 'categories'
    ];

    public function boothItems()
    {
        return $this->hasMany('App\Models\Booth', 'hall_id', 'id');
    }
}
