<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'icon_path',
    ];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, "")
            ->withTimestamps();
    }
}
