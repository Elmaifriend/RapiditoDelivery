<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Product;
use App\Models\Schedule;
use App\Enums\BusinessStatus;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        "phone",
        "email",
        "web_site",
        'status',
        'category_id',
        'address',
        'city_id',
        'state',
        'postal_code',
        'country',
        'lat',
        'lng',
        'google_maps_url',
        'logo_path',
        'banner_path',
        'reference_image',
        'is_open',
        'accepts_delivery',
        'accepts_pickup',
    ];

    protected $casts = [
        'status' => BusinessStatus::class,
        'is_open' => 'boolean',
        'accepts_delivery' => 'boolean',
        'accepts_pickup' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'scheduleable');
    }

    public function products()
    {
        return $this->hasMany(Product::class, "business_id");
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, "business_tag", "business_id", "tag_id")
            ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'business_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}