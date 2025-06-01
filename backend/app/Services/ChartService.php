<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ChartService
{
    public function generateChart(array $deliveries, array $shipments, array $grains, int $warehouseId): string
    {
        $labels = collect($deliveries)
            ->pluck('delivery_date')
            ->merge(collect($shipments)->pluck('shipment_date'))
            ->unique()
            ->sort()
            ->values();

        $grainMap = collect($grains)->mapWithKeys(fn ($g) => [$g['grain_type']['id'] => $g['grain_type']['name']]);

        $datasets = [];

        foreach ($grainMap as $id => $name) {
            $datasets[] = [
                'label' => $name . ' — Поставка',
                'backgroundColor' => 'rgba(54,162,235,0.7)',
                'data' => $labels->map(fn ($date) => collect($deliveries)
                    ->where('grain_type_id', $id)
                    ->where('delivery_date', $date)
                    ->sum('volume'))
                    ->values()
            ];
            $datasets[] = [
                'label' => $name . ' — Отгрузка',
                'backgroundColor' => 'rgba(255,99,132,0.7)',
                'data' => $labels->map(fn ($date) => collect($shipments)
                    ->where('grain_type_id', $id)
                    ->where('shipment_date', $date)
                    ->sum('volume'))
                    ->values()
            ];
        }

        $chartConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'title' => ['display' => true, 'text' => 'Поставки и отгрузки по культурам'],
                'legend' => ['position' => 'bottom'],
            ],
        ];

        $response = Http::get('https://quickchart.io/chart', [
            'c' => json_encode($chartConfig),
            'format' => 'png',
            'width' => 1000,
            'height' => 400
        ]);

        $filename = 'charts/chart_' . $warehouseId . '_' . Str::random(5) . '.png';
        Storage::disk('public')->put($filename, $response->body());

        return 'storage/' . $filename; // путь для использования в public_path()
    }
}
