<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'title',
        'content',
        'class_id', // optional, tapi bagus kalau nak mass assign semua
    ];

    public function classModel()
    {
        return $this->belongsTo(\App\Models\ClassModel::class, 'class_id');
    }
}