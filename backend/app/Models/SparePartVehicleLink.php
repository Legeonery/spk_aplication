<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartVehicleLink extends Model
{
    protected $guarded = false;

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
