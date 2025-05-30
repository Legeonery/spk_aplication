<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrainTypes extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function warehouseGrains()
    {
        return $this->hasMany(WarehouseGrains::class, 'grain_type_id');
    }
}
