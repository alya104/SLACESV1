<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Enrollment;
use App\Models\User;

class ClassModel extends Model
{
    use HasFactory;
    
    protected $table = 'classes';

    protected $fillable = [
        'title',               // Updated from 'title'
        'description',
        'status',            // Updated from 'is_active'
        'thumbnail',          // Added new field
        'start_date',        // Added new field
        'end_date'           // Added new field
    ];
    
    protected $casts = [
        'price' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the enrollments for this class
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }
    
    /**
     * Get the students enrolled in this class
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'class_id', 'user_id')
                    ->where('role', 'student');
    }
    
    /**
     * Get the modules for this class
     */
    public function modules()
    {
        return $this->hasMany(\App\Models\Module::class, 'class_id');
    }
    
    /**
     * Get the invoices associated with this class
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'class_id');
    }
    
    /**
     * Get the instructor for this class
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    /**
     * Check if class is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Get the enrollment count
     */
    public function getEnrollmentCountAttribute()
    {
        return $this->enrollments()->count();
    }
    
    /**
     * Get the class associated with the enrollment
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    /**
     * Get the materials for this class
     */
    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class, 'class_id');
    }
}
