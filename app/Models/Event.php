<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['domain_id', 'start', 'end', 'timezone', 'date'];

    public function session()
    {
        return $this->hasMany('App\Models\Session', 'event_id', 'id')->orderBy('start', 'asc');
    }

    public function sessions() {
      $sessionData = Session::where('event_id', $this->id)
        ->with('track')
        ->with('event')
        ->orderBy('event_id', 'asc')
        ->get();

      return $sessionData;
    }
}
