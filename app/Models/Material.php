<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'class_id',
        'type',
        'file_path',
        'url',
        'thumbnail',
        'is_active',
        'sort_order'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Get the class that the material belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    /**
     * Alternative to the class() method to avoid PHP keyword conflicts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    /**
     * Get the URL for this material (either direct URL or storage path)
     *
     * @return string|null
     */
    public function getResourceUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        
        return null;
    }
    
    /**
     * Get the thumbnail URL for this material
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        
        // Default thumbnails based on material type
        return asset('images/default-' . $this->type . '.png');
    }
}
