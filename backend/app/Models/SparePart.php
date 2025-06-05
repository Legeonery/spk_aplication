<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $guarded = false;

    public function warehouses()
    {
        return $this->belongsToMany(Warehouses::class, 'warehouse_spare_parts')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseSparePart::class, 'spare_part_id');
    }

    public function movements()
    {
        return $this->hasMany(SparePartMovement::class, 'spare_part_id');
    }

    public function vehicleLinks()
    {
        return $this->hasMany(SparePartVehicleLink::class, 'spare_part_id');
    }
    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
    public function requests()
    {
        return $this->hasMany(SparePartRequest::class);
    }
    public function warehouseQuantities()
    {
        return $this->hasMany(WarehouseSparePart::class);
    }
}
