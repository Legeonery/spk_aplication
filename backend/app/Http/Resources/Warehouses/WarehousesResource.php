<?php

namespace App\Http\Resources\Warehouses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehousesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'area' => $this->area,
            'status' => $this->status,
            'max_historical_load' => $this->max_historical_load,
        ];
    }
}
