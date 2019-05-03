<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Caller extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'phone', 'city',
        'state', 'country'
    ];

    public function agent()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function calls()
    {
        return $this->hasMany('App\Call');
    }
}
