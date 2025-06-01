<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    protected $guarded = false;
    public function warehouseGrains()
    {
        return $this->hasMany(\App\Models\WarehouseGrains::class, 'warehouse_id');
    }
    public function grains()
    {
        return $this->hasMany(WarehouseGrains::class, 'warehouse_id');
    }

    public function grainDeliveries()
    {
        return $this->hasMany(GrainDelivery::class, 'warehouse_id');
    }

    public function grainShipments()
    {
        return $this->hasMany(GrainShipment::class, 'warehouse_id');
    }
}
