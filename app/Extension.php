<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'number', 'name', 'message',
        'transfer_prompt', 'voicemail_prompt'
    ];

    public function calls()
    {
        return $this->hasMany('App\Call');
    }

    public function agents()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }
}
