<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'id',
        'domain_id',
        'category_id',
        'track_id',
        'event_id',
        'title',
        'button',
        'pageType',
        'description',
        'hashtag',
        'presenter',
        'start',
        'end',
        'url'
    ];

    // todo: should have track, day along with title here
    public static function findOrNew($data) {
        $exists = self::where('title', $data['title'])->first();
        if ($exists) return $exists;
        else {
            $exists = self::create($data);
        }

        return $exists;
    }

    public function category()
    {
        return $this->hasOne('App\Models\TrackCategory', 'id', 'category_id');
    }

    public function track()
    {
        return $this->hasOne('App\Models\Track', 'id', 'track_id');
    }

    public function page()
    {
        return $this->hasOne('App\Models\Page', 'id', 'page_id');
    }

    public function event()
    {
        return $this->hasOne('App\Models\Event', 'id', 'event_id');
    }

    public function videos()
    {
        return $this->hasMany('App\Models\Video', 'session_id');
    }

    public function uploads()
    {
        return $this->hasMany('App\Models\SessionUpload', 'session_id');
    }
}
