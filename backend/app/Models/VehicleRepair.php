<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRepair extends Model
{
    protected $fillable = ['vehicle_id', 'reason'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}