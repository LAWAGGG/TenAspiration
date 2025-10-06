<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AspirationEvent extends Model
{
    protected $guarded = [];

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
