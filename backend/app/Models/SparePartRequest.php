<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SparePartRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'spare_part_id',
        'custom_name',
        'vehicle_id',
        'user_id',
        'quantity',
        'note',
        'status'
    ];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spare_part()
    {
        return $this->belongsTo(SparePart::class);
    }
}
