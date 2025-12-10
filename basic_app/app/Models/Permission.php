<?php // app/Models/Permission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'name_en',
        'module_name',
        'name_ar',
        'can_edit',
        'can_delete',
        'can_add',
        'can_view_history',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'can_edit'         => 'boolean',
        'can_delete'       => 'boolean',
        'can_add'          => 'boolean',
        'can_view_history' => 'boolean',
        'is_active'        => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id'); // adjust class if needed
    }
       public function user()
    {
        return $this->belongsTo(User::class, 'user_id');         }
}

