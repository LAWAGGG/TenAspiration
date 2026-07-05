<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AspirationKeluhKesah extends Model
{
    protected $table = "aspiration_keluh_kesah";
    protected $fillable = ['phone_number', 'keluh_kesah', 'custom_answers'];

    protected $casts = [
        'custom_answers' => 'array',
    ];
}
