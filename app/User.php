<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function formattedPhone()
    {
        $raw = Auth::user()->phone;
        $formatted = sprintf("(%s) %s-%s",
            substr($raw, 0, 3),
            substr($raw, 3, 3),
            substr($raw, 6)
        );

        return $formatted;
    }

    public function extensions()
    {
        return $this->belongsToMany('App\Extension')->withTimestamps();
    }
}
