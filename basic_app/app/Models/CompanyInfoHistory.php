<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class CompanyInfoHistory extends Model
{
     use HasFactory, Notifiable;
    //
    protected $table = 'company_info_history'; // Ensure the table name is correct
    protected $fillable = [
        'image',
        'name_en',
        'name_ar',
        'phone',
        'email',
        'address_en',
        'address_ar',
        'location',
        'main_color',
        'sub_color',
        'text_color',
        'button_color',
        'icon_color',
        'text_filed_color',
        'hint_color',
        'button_text_color',
        'card_color',
        'label_color',
        'about_us_en',
        'about_us_ar',
        'mission_en',
        'mission_ar',
        'vision_en',
        'vision_ar',
        'user_id',
    ];
    protected $casts = ['created_at' => 'datetime',];
    public function user(){return $this->belongsTo(User::class, 'user_id');}
    public static function company(){
    $this->belongs(CompanyInfo::class,'company_info_id');;
}



}
