<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class District extends Model
{
    protected $table = 'crm.district';
    protected $primaryKey = 'DISTRICT_ID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'DISTRICT_ID',
        'DISTRICT_CODE',
        'DISTRICT_NAME',
        'STATE_ID',
        'COUNTRY_ID',
        'LOCK_STATUS',
        'LOG_USERID',
        'LOG_TIMESTAMP',
        'LOG_IPADDRESS',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'STATE_ID', 'state_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'COUNTRY_ID', 'country_id');
    }

    protected static function booted(): void
    {
        static::saved(function ($district) {
            static::clearDistrictCache($district);
        });

        static::deleted(function ($district) {
            static::clearDistrictCache($district);
        });
    }

    /**
     * Clear all cached district entries.
     */
    public static function clearDistrictCache(?self $district = null): void
    {
        try {
            Cache::forget('geo_districts_all');
            Cache::forget('geo_districts_active');
            if ($district && !empty($district->STATE_ID)) {
                Cache::forget('geo_districts_state_' . (int)$district->STATE_ID);
            }
            if ($district && !empty($district->DISTRICT_ID)) {
                Cache::forget('geo_district_id_' . (int)$district->DISTRICT_ID);
                Cache::forget('geo_district_name_' . (int)$district->DISTRICT_ID);
            }
        } catch (\Throwable $e) {
            Log::warning('[District::clearDistrictCache] ' . $e->getMessage());
        }
    }

    /**
     * Get cached districts by State ID (cached for 24 hours).
     */
    public static function cachedByState(int $stateId): Collection
    {
        $cacheKey = 'geo_districts_state_' . $stateId;

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($stateId) {
            $districts = collect();
            try {
                $districts = static::where('STATE_ID', $stateId)
                    ->where('LOCK_STATUS', 0)
                    ->select('DISTRICT_ID', 'DISTRICT_CODE', 'DISTRICT_NAME', 'STATE_ID', 'COUNTRY_ID')
                    ->orderBy('DISTRICT_NAME')
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('[District::cachedByState] ' . $e->getMessage());
            }

            if ($districts->isEmpty()) {
                if ($stateId === 1) { // Andhra Pradesh
                    $districts = collect([
                        (object)['DISTRICT_ID' => 1, 'DISTRICT_CODE' => 'GNT', 'DISTRICT_NAME' => 'Guntur', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 2, 'DISTRICT_CODE' => 'KRI', 'DISTRICT_NAME' => 'Krishna', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 3, 'DISTRICT_CODE' => 'VSP', 'DISTRICT_NAME' => 'Visakhapatnam', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 4, 'DISTRICT_CODE' => 'VJY', 'DISTRICT_NAME' => 'Vijayawada', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 5, 'DISTRICT_CODE' => 'PRA', 'DISTRICT_NAME' => 'Prakasam', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 6, 'DISTRICT_CODE' => 'NEL', 'DISTRICT_NAME' => 'Nellore', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    ]);
                } elseif ($stateId === 2 || $stateId === 24) { // Telangana
                    $districts = collect([
                        (object)['DISTRICT_ID' => 10, 'DISTRICT_CODE' => 'HYD', 'DISTRICT_NAME' => 'Hyderabad', 'STATE_ID' => $stateId, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 11, 'DISTRICT_CODE' => 'RR', 'DISTRICT_NAME' => 'Ranga Reddy', 'STATE_ID' => $stateId, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 12, 'DISTRICT_CODE' => 'MDK', 'DISTRICT_NAME' => 'Medchal-Malkajgiri', 'STATE_ID' => $stateId, 'COUNTRY_ID' => 1],
                        (object)['DISTRICT_ID' => 13, 'DISTRICT_CODE' => 'WGL', 'DISTRICT_NAME' => 'Warangal', 'STATE_ID' => $stateId, 'COUNTRY_ID' => 1],
                    ]);
                }
            }

            return $districts;
        });
    }

    /**
     * Get all cached districts (cached for 24 hours).
     */
    public static function cachedAll(): Collection
    {
        $cacheKey = 'geo_districts_all';

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            $districts = collect();
            try {
                $districts = static::where('LOCK_STATUS', 0)
                    ->select('DISTRICT_ID', 'DISTRICT_CODE', 'DISTRICT_NAME', 'STATE_ID', 'COUNTRY_ID')
                    ->orderBy('DISTRICT_NAME')
                    ->get();
            } catch (\Throwable $e) {
                Log::warning('[District::cachedAll] ' . $e->getMessage());
            }

            if ($districts->isEmpty()) {
                $districts = collect([
                    (object)['DISTRICT_ID' => 1, 'DISTRICT_CODE' => 'GNT', 'DISTRICT_NAME' => 'Guntur', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 2, 'DISTRICT_CODE' => 'KRI', 'DISTRICT_NAME' => 'Krishna', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 3, 'DISTRICT_CODE' => 'VSP', 'DISTRICT_NAME' => 'Visakhapatnam', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 4, 'DISTRICT_CODE' => 'VJY', 'DISTRICT_NAME' => 'Vijayawada', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 5, 'DISTRICT_CODE' => 'PRA', 'DISTRICT_NAME' => 'Prakasam', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 6, 'DISTRICT_CODE' => 'NEL', 'DISTRICT_NAME' => 'Nellore', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 10, 'DISTRICT_CODE' => 'HYD', 'DISTRICT_NAME' => 'Hyderabad', 'STATE_ID' => 24, 'COUNTRY_ID' => 1],
                    (object)['DISTRICT_ID' => 11, 'DISTRICT_CODE' => 'RR', 'DISTRICT_NAME' => 'Ranga Reddy', 'STATE_ID' => 24, 'COUNTRY_ID' => 1],
                ]);
            }

            return $districts;
        });
    }

    /**
     * Find a cached district by ID (cached for 24 hours).
     */
    public static function cachedFind(int $districtId): ?self
    {
        $cacheKey = 'geo_district_id_' . $districtId;

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($districtId) {
            try {
                return static::where('DISTRICT_ID', $districtId)->first();
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    /**
     * Get a cached district name by ID (cached for 24 hours).
     */
    public static function cachedName(int $districtId): ?string
    {
        $cacheKey = 'geo_district_name_' . $districtId;

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($districtId) {
            try {
                return static::where('DISTRICT_ID', $districtId)->value('DISTRICT_NAME');
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
