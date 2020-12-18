<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainUser extends Model
{
    protected $table = 'domain_users';

    protected $fillable = ['user_id', 'domain_id', 'code', 'status'];
}
