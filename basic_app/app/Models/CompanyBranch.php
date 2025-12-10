<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class CompanyBranch extends Model
{
    //
    use HasFactory, Notifiable;
    protected $table = 'company_branches'; // Ensure the table name is correct
    protected $fillable = [
        'company_info_id',
        'name_en',
        'name_ar',
        'is_active',
        'phone',
        'email',
        'address_en',
        'address_ar',
        'location',
        'image',
        'working_hours',
        'working_days','working_hours_from','working_hours_to', 'fax',
    ];
    protected $casts = [
        'is_main_branch' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activeBranches(){
        return $this->where('is_active',true);
    }
public function category(){return $this->hasMany(Category::class);}

public function categoryHistory(){return $this->hasMany(CategoryHistory::class);}

public static function getBranches(bool $isHistory = false)
{
    return self::where('is_active', 1);
}

public function user(){return $this->belongsTo(User::class, 'user_id');}
public function companyInfo()
{
    return $this->belongsTo(CompanyInfo::class, 'company_id');

}
}
