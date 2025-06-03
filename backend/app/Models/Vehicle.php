<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'type',
        'max_weight',
        'status',
        'driver_id',
        'is_available',
        'notes',
        'vehicle_kind_id',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // 🔧 Связь с использованием запчастей
    public function sparePartLinks()
    {
        return $this->hasMany(SparePartVehicleLink::class, 'vehicle_id');
    }

    public function usedSpareParts()
    {
        return $this->belongsToMany(SparePart::class, 'spare_part_vehicle_links')
            ->withPivot(['used_at', 'note'])
            ->withTimestamps();
    }
    public function vehicleKind()
    {
        return $this->belongsTo(VehicleKind::class);
    }
    public function tareMeasurement()
    {
        return $this->hasOne(TareMeasurement::class)->latestOfMany();
    }
    public function tareMeasurements()
    {
        return $this->hasMany(TareMeasurement::class);
    }

    public function latestTareMeasurement()
    {
        return $this->hasOne(TareMeasurement::class)->latestOfMany();
    }
}
