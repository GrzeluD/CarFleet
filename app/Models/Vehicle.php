<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $primaryKey = 'vehicle_id';
    protected $fillable = [
        'brand',
        'model',
        'vin',
        'production_year',
        'license_plate',
        'insurance_expiry',
        'inspection_due',
    ];

    public function mileage(): HasMany
    {
        return $this->hasMany(VehicleMileage::class, 'vehicle_id', 'vehicle_id');
    }

    public function costs(): HasMany
    {
        return $this->hasMany(VehicleCost::class, 'vehicle_id', 'vehicle_id');
    }
}
