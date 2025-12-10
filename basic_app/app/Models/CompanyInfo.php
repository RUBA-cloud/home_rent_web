<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class CompanyInfo extends Model
{
    use HasFactory, Notifiable;

    // Specify the table name if not following Laravel's naming convention
    protected $table = 'company_info';
 protected $dispatchesEvents = [
        'created' => \App\Events\CompanyInfoEventSent::class,
        'updated' => \App\Events\CompanyInfoEventSent::class,
        'deleted' => \App\Events\CompanyInfoEventSent::class,
    ];
    // Specify the fillable fields for mass assignment
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
    ];

    // Cast attributes as needed (example: date formats)
    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Accessor for logo image to get the full URL
    public function getLogoImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    // Mutator for setting the logo image (handles file uploads)
    public function setLogoImageAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            // If it's a string (e.g., URL), set the value directly
            $this->attributes['logo_image'] = $value;
        } elseif ($value instanceof \Illuminate\Http\UploadedFile) {
            // If it's a file, store it
            $this->attributes['logo_image'] = $value->store('company_logos', 'public');
        } else {
            // If no image is provided, set it to null
            $this->attributes['logo_image'] = null;
        }
    }

    // Accessor for 'about_us' to return as an array
    public function getAboutUsAttribute()
    {
        return [
            'en' => $this->about_us_en ?? '',
            'ar' => $this->about_us_ar ?? '',
        ];
    }

    // Accessor for 'vision' to return as an array
    public function getVisionAttribute()
    {
        return [
            'en' => $this->vision_en ?? '',
            'ar' => $this->vision_ar ?? '',
        ];
    }

    // Accessor for 'about_us_en'
    public function getAboutUsEnAttribute($value)
    {
        return $value ?? '';
    }

    // Accessor for 'about_us_ar'
    public function getAboutUsArAttribute($value)
    {
        return $value ?? '';
    }

    // Accessor for 'mission_en'
    public function getMissionEnAttribute($value)
    {
        return $value ?? '';
    }

    // Accessor for 'mission_ar'
    public function getMissionArAttribute($value)
    {
        return $value ?? '';
    }

    // Accessor for 'vision_en'
    public function getVisionEnAttribute($value)
    {
        return $value ?? '';
    }

    // Accessor for 'vision_ar'
    public function getVisionArAttribute($value)
    {
        return $value ?? '';
    }
     public function getCreatedAtHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    // Event listeners to broadcast changes when updated
    protected static function booted()
    {
        static::updated(function ($companyInfo) {
            // Broadcast the updated company info, if needed
            broadcast(new \App\Events\CompanyInfoEventSent($companyInfo));
        });

        static::created(function ($companyInfo) {
            // Broadcast the newly created company info, if needed
            broadcast(new \App\Events\CompanyInfoEventSent($companyInfo));
        });
    }
}
