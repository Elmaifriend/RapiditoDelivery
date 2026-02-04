<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, "restaurant_user", "restaurant_id", "user_id");
    }
}

