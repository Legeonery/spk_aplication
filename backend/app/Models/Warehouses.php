<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    protected $guarded = false;

    // Зерновые
    public function warehouseGrains()
    {
        return $this->hasMany(WarehouseGrains::class, 'warehouse_id');
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

    // 🔧 Запчасти
    public function spareParts()
    {
        return $this->hasMany(WarehouseSparePart::class, 'warehouse_id');
    }

    public function sparePartDeliveries()
    {
        return $this->hasMany(SparePartMovement::class, 'warehouse_id')
            ->where('type', 'in');
    }

    public function sparePartUsages()
    {
        return $this->hasMany(SparePartMovement::class, 'warehouse_id')
            ->where('type', 'out');
    }
}
