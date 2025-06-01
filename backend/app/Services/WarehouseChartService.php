<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class WarehouseChartService
{
    public function generateChart($warehouse): ?string
    {
        $deliveries = $warehouse->grainDeliveries->groupBy('delivery_date');
        $shipments  = $warehouse->grainShipments->groupBy('shipment_date');

        $allDates = collect($deliveries->keys())->merge($shipments->keys())->unique()->sort()->values();
        $labels = $allDates->toArray();

        $cultureNames = $warehouse->grainDeliveries
            ->pluck('grainType.name')
            ->merge($warehouse->grainShipments->pluck('grainType.name'))
            ->unique()
            ->values();

        $datasets = [];

        foreach ($cultureNames as $culture) {
            // Поставка
            $datasets[] = [
                'label' => $culture . ' — Поставка',
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                'data' => collect($labels)->map(fn($date) => optional($deliveries[$date])->where('grainType.name', $culture)->sum('volume') ?? 0)->toArray()
            ];

            // Отгрузка
            $datasets[] = [
                'label' => $culture . ' — Отгрузка',
                'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                'data' => collect($labels)->map(fn($date) => optional($shipments[$date])->where('grainType.name', $culture)->sum('volume') ?? 0)->toArray()
            ];
        }

        $chartData = json_encode([
            'labels' => $labels,
            'datasets' => $datasets
        ]);

        $filename = 'charts/' . Str::uuid() . '.png';
        $chartPath = public_path($filename);

        $process = new Process([
            'node',
            base_path('node/chart/generateChart.js'),
            $chartData,
            $chartPath
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            logger()->error('Ошибка генерации графика: ' . $process->getErrorOutput());
            return null;
        }

        return $filename;
    }
}
