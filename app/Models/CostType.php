<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostType extends Model
{
    protected $primaryKey = 'cost_type_id';
    protected $fillable = ['cost_type_name'];

    public function vehicleCosts(): HasMany
    {
        return $this->hasMany(VehicleCost::class, 'cost_type_id', 'cost_type_id');
    }
}
