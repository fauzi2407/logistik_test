<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_province',
        'origin_city',
        'origin_district',
        'origin_subdistrict',
        'destination_province',
        'destination_city',
        'destination_district',
        'destination_subdistrict',
        'service_type',
        'price_per_kg',
        'min_weight_kg',
        'estimated_days',
    ];

    public static function findTariff($originRaw, $destinationRaw, $serviceType = 'Express', $originDistrict = null, $originSubdistrict = null, $destinationDistrict = null, $destinationSubdistrict = null)
    {
        $originClean = preg_replace('/^(Kota|Kab\.?|Kabupaten)\s+/i', '', trim($originRaw ?? ''));
        $destinationClean = preg_replace('/^(Kota|Kab\.?|Kabupaten)\s+/i', '', trim($destinationRaw ?? ''));

        $query = static::where(function($q) use ($originRaw, $originClean) {
                $q->where('origin_city', $originRaw)
                  ->orWhere('origin_city', $originClean)
                  ->orWhere('origin_city', 'like', "%{$originClean}%");
            })
            ->where(function($q) use ($destinationRaw, $destinationClean) {
                $q->where('destination_city', $destinationRaw)
                  ->orWhere('destination_city', $destinationClean)
                  ->orWhere('destination_city', 'like', "%{$destinationClean}%");
            })
            ->where('service_type', $serviceType);

        if ($destinationSubdistrict) {
            $specificSubdist = (clone $query)->where('destination_subdistrict', $destinationSubdistrict)->first();
            if ($specificSubdist) return $specificSubdist;
        }

        if ($destinationDistrict) {
            $specificDist = (clone $query)->where('destination_district', $destinationDistrict)->first();
            if ($specificDist) return $specificDist;
        }

        return $query->first();
    }
}
