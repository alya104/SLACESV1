<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['admin_id', 'action', 'description'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public static function logAction($adminId, $action, $description)
    {
        self::create([
            'admin_id' => $adminId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}