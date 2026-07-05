<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetEmail extends Model
{
    protected $fillable = ['email', 'is_active'];
}
