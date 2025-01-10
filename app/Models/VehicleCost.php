<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleCost extends Model
{
    protected $primaryKey = 'cost_id';
    protected $fillable = [
        'vehicle_id',
        'date',
        'cost_type_id',
        'description',
        'amount_gross',
        'vat_rate',
        'amount_net',
        'vat_amount',
        'invoice_path'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }

    public function costType(): BelongsTo
    {
        return $this->belongsTo(CostType::class, 'cost_type_id', 'cost_type_id');
    }

    public function getCostTypeNameAttribute()
    {
        return $this->costType ? $this->costType->cost_type_name : null;
    }
}
