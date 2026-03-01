<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Location\Coordinate;
use Location\Polygon as GeoPolygon;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_zone_id',
        'name',
        'polygon_json',
        'delivery_price',
        'priority',
        'active',
    ];

    protected $casts = [
        'polygon_json' => 'array',
        'delivery_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot: calcular BBOX automáticamente
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saving(function ($zone) {

            $coordinates = $zone->polygon_json['coordinates'][0] ?? [];

            $lats = [];
            $lngs = [];

            foreach ($coordinates as $point) {
                $lngs[] = $point[0];
                $lats[] = $point[1];
            }

            $zone->bbox_min_lat = min($lats);
            $zone->bbox_max_lat = max($lats);
            $zone->bbox_min_lng = min($lngs);
            $zone->bbox_max_lng = max($lngs);
        });
    }

    public function serviceZone()
    {
        return $this->belongsTo(ServiceZone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeWhereBboxContains($query, float $lat, float $lng)
    {
        return $query
            ->where('bbox_min_lat', '<=', $lat)
            ->where('bbox_max_lat', '>=', $lat)
            ->where('bbox_min_lng', '<=', $lng)
            ->where('bbox_max_lng', '>=', $lng);
    }

    public function toPhpGeoPolygon(): GeoPolygon
    {
        $polygon = new GeoPolygon();

        $coordinates = $this->polygon_json['coordinates'][0] ?? [];

        foreach ($coordinates as $point) {
            $polygon->addPoint(
                new Coordinate($point[1], $point[0]) // lat, lng
            );
        }

        return $polygon;
    }

    public function contains(float $lat, float $lng): bool
    {
        if (!$this->active) {
            return false;
        }

        $coordinate = new Coordinate($lat, $lng);

        return $this->toPhpGeoPolygon()->contains($coordinate);
    }

    public function outgoingFares()
    {
        return $this->hasMany(DeliveryZoneFare::class, 'from_zone_id');
    }

    public function incomingFares()
    {
        return $this->hasMany(DeliveryZoneFare::class, 'to_zone_id');
    }
}