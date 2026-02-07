<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'min',
        'max',
        'required',
        'position',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function options()
    {
        return $this->hasMany(Option::class, "option_group_id");
    }
}
