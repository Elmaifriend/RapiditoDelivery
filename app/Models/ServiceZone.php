<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Location\Coordinate;
use Location\Polygon as GeoPolygon;

class ServiceZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'polygon',
        'active',
        'debug',
        'min_lat',
        'max_lat',
        'min_lng',
        'max_lng',
    ];

    protected $casts = [
        'polygon' => 'array',
        'active' => 'boolean',
        'debug'  => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($zone) {

            $coordinates = [];

            if ( isset($zone->polygon['features'][0]['geometry']['type']) && $zone->polygon['features'][0]['geometry']['type'] === 'Polygon' ) {
                $coordinates = $zone->polygon['features'][0]['geometry']['coordinates'][0] ?? [];
            }

            $lats = [];
            $lngs = [];

            foreach ($coordinates as $point) {
                $lngs[] = $point[0];
                $lats[] = $point[1];
            }

            $zone->min_lat = min($lats);
            $zone->max_lat = max($lats);
            $zone->min_lng = min($lngs);
            $zone->max_lng = max($lngs);
        });
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Convert stored GeoJSON into phpgeo Polygon
     */
    public function toPhpGeoPolygon(): GeoPolygon
    {
        $polygon = new GeoPolygon();

        $coordinates = $this->polygon['features'][0]['geometry']['coordinates'][0] ?? [];

        foreach ($coordinates as $point) {
            $polygon->addPoint(new Coordinate($point[1], $point[0]));
        }

        return $polygon;
    }

    /**
     * Check if point is inside zone
     */
    public function contains(float $lat, float $lng): bool
    {
        if (!$this->active) {
            return false;
        }

        // BBOX check (mucho más rápido)
        if (
            $lat < $this->min_lat ||
            $lat > $this->max_lat ||
            $lng < $this->min_lng ||
            $lng > $this->max_lng
        ) {
            return false;
        }

        $coordinate = new Coordinate($lat, $lng);

        return $this->toPhpGeoPolygon()->contains($coordinate);
    }

    /**
     * Scope: only active zones
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope: debug zones
     */
    public function scopeDebug($query)
    {
        return $query->where('debug', true);
    }
}