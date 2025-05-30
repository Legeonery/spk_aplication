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
}
