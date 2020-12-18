<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'domain_id',
        'session_id',
        'title',
        'format',
        'kind',
        'link',
        'description',
        'hashtag',
        'presenter',
        'isChat',
        'isNote',
        'code',
        'status',
        'stream_url',
        'stream_name',
        'stream_active',
        'zoom_response'
    ];

    public function session()
    {
        return $this->hasOne('App\Models\Session', 'id', 'session_id');
    }
}
