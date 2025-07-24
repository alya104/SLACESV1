<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'class_id',
        'status',
        'enrollment_date',
        'completion_percentage'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'enrollment_date' => 'date',
        'completion_percentage' => 'integer'
    ];

    /**
     * Get the user associated with the enrollment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the student associated with the enrollment
     */
    public function student() { return $this->belongsTo(User::class, 'user_id'); }

    /**
     * Get the class associated with the enrollment
     */
    public function class() { return $this->belongsTo(ClassModel::class, 'class_id'); }

    /**
     * Alias for the class relationship to avoid PHP keyword conflicts
     */
    public function classModel()
    {
        return $this->class();
    }

    /**
     * Get the progress percentage for this enrollment
     */
    public function getProgressAttribute()
    {
        // If you have a completion_percentage field, return that
        if ($this->attributes['completion_percentage'] ?? null) {
            return $this->attributes['completion_percentage'];
        }
        
        // Otherwise return 0 or calculate from related models
        return 0;
    }
    
    /**
     * Check if enrollment is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    /**
     * Check if enrollment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Get the progress records associated with this enrollment
     */
    public function progress()
    {
        return $this->hasMany(\App\Models\Progress::class, 'user_id', 'user_id')
            ->where('class_id', $this->class_id);
    }
}