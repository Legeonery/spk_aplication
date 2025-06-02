<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'tare_weight',
        'delivery_count',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}