<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseGrain extends Model
{
    protected $fillable = ['warehouse_id', 'grain_type_id', 'amount'];

    public function grainType()
    {
        return $this->belongsTo(GrainTypes::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouses::class);
    }
}
