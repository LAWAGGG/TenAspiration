<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspiration extends Model
{
    protected $fillable = ['message', 'kelas', 'to', 'custom_answers'];

    protected $casts = [
        'custom_answers' => 'array',
    ];
}
