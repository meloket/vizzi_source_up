<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public function booth()
    {
        return $this->hasOne('App\Models\Booth', 'id', 'item_id');
    }
    public function poster()
    {
        return $this->hasOne('App\Models\Poster', 'id', 'item_id');
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
