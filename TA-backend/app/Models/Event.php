<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    public function aspirationEvents()
    {
        return $this->hasMany(AspirationEvent::class);
    }
}
