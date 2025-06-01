<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseSparePart extends Model
{
    protected $guarded = false;

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class, 'warehouse_id');
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
