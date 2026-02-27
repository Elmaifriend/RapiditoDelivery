<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Product;

class Restaurant extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'status',
        'category_id',
        'address',
        'city',
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

    public function products(){
        return $this->hasMany(Product::class, "restaurant_id");
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, "restaurant_tag", "restaurant_id", "tag_id")
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "restaurant_user", "restaurant_id", "user_id");
    }

    // Delivery zones (polígonos)
    public function deliveryZones()
    {
        //return $this->hasMany(DeliveryZone::class);
    }

    /* ======================
     | Scopes útiles
     ====================== */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }
}


