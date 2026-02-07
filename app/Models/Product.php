<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'price',
        'is_active',
        'is_available',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_available' => 'boolean',
    ];

    /* ======================
     | Relaciones
     ====================== */

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /* ======================
     | Scopes
     ====================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
