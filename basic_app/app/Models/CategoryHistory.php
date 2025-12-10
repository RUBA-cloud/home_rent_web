<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryHistory extends Model
{
    //
protected $table = 'categories_history';
protected $fillable=['name_en','name_ar','is_active','image'];

 // Ensure the table name is correct

public function branches()
{
return $this->belongsToMany(CompanyBranch::class, 'category_branch_history', 'category_id', 'branch_id');
}
public function user(){return $this->belongsTo(User::class, 'user_id');}

}
