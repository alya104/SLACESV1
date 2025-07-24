<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'class_id',
        'amount',
        'status',
        'invoice_date',
        'email', // optional, if you want to store invoice email separately
    ];

    protected $dates = ['invoice_date'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }
}