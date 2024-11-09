<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMileage extends Model
{
    protected $primaryKey = 'mileage_id';
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'date',
        'start_mileage',
        'end_mileage',
        'location_start',
        'location_end',
        'route_description',
        'distance_traveled'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }
}