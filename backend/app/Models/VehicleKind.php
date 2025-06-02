<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleKind extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 🔁 Один ко многим: один вид ТС может быть у многих vehicles
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
