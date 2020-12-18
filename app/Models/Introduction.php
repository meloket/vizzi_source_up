<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Introduction extends Model
{
    protected $fillable = ['user_id', 'domain_id', 'title', 'content'];
}
