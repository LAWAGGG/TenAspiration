<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedLink extends Model
{
    protected $fillable = ['token', 'type', 'selected_ids', 'filters', 'title', 'created_by'];

    protected $casts = [
        'selected_ids' => 'array',
        'filters' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
