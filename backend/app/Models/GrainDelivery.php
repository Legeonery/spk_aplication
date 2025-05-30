<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrainDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'grain_type_id', // исправлено с 'grain_type' на 'grain_type_id'
        'volume',
        'delivery_date',
        'vehicle_id',
        'driver_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function grainType()
    {
        return $this->belongsTo(GrainTypes::class, 'grain_type_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}