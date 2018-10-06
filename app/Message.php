<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Message extends Model
{
    protected $fillable = [
        'body', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getCreatedAtAttribute($date)
    {
        $d = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        return $d->diffForHumans();
    }
}