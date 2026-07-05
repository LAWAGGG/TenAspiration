<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AspirationEvent extends Model
{
    protected $fillable = ['event_id', 'message', 'kesan_pesan', 'perubahan_dari_event', 'bad_moment', 'custom_answers'];

    protected $casts = [
        'custom_answers' => 'array',
    ];

    public function event(){
        return $this->belongsTo(Event::class);
    }
}
