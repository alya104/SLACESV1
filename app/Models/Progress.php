<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'module_id',
        'completed',
        'completed_at',
    ];
}
