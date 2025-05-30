<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseGrains extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'grain_type_id',
        'amount',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function grainType()
    {
        return $this->belongsTo(GrainTypes::class, 'grain_type_id');
    }
}