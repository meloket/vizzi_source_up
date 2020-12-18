<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterSetting extends Model
{
    protected $fillable = ['domain_id', 'label', 'required', 'disabled', 'order', 'user_id'];
}
