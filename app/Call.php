<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'caller_id', 'extension_id', 'sid'
    ];

    public function caller()
    {
        return $this->belongsTo('App\Caller');
    }

    public function extension()
    {
        return $this->belongsTo('App\Extension');
    }
}
