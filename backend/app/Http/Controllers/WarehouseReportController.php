<?php

namespace App\Http\Controllers;

use App\Models\Warehouses;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\Process\Process;

class WarehouseReportController extends Controller
{
    public function generate($id)
    {
        $warehouse = Warehouses::with([
            'grains.grainType',
            'grainDeliveries.grainType',
            'grainDeliveries.driver',
            'grainShipments.grainType',
            'grainShipments.driver'
        ])->findOrFail($id);

        $reportDate = now()->format('d.m.Y H:i');

        // === Подготовка данных для графика ===
        $labels = [];
        $datasetsMap = [];

        foreach ($warehouse->grainDeliveries as $d) {
            $labels[] = $d->delivery_date;
            $key = $d->grainType->name . ' — Поставка';
            $datasetsMap[$key][$d->delivery_date] = ($datasetsMap[$key][$d->delivery_date] ?? 0) + $d->volume;
        }

        foreach ($warehouse->grainShipments as $s) {
            $labels[] = $s->shipment_date;
            $key = $s->grainType->name . ' — Отгрузка';
            $datasetsMap[$key][$s->shipment_date] = ($datasetsMap[$key][$s->shipment_date] ?? 0) + $s->volume;
        }

        $labels = array_values(array_unique($labels));
        sort($labels);

        $datasets = [];
        foreach ($datasetsMap as $label => $data) {
            $datasets[] = [
                'label' => $label,
                'data' => array_map(fn($date) => $data[$date] ?? 0, $labels),
                'backgroundColor' => str_contains($label, 'Поставка') ? 'rgba(54,162,235,0.7)' : 'rgba(255,99,132,0.7)',
            ];
        }

        // === Генерация изображения ===
        $chartPath = "storage/reports/chart_warehouse_$id.png";
        $absPath = public_path($chartPath);
        $json = json_encode(['labels' => $labels, 'datasets' => $datasets]);

        $process = new Process(['node', base_path('node/chart/generateChart.js'), $json, $absPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            logger()->error('Ошибка генерации графика: ' . $process->getErrorOutput());
            $chartPath = null;
        }

        $pdf = Pdf::loadView('reports.warehouse', [
            'warehouse' => $warehouse,
            'reportDate' => $reportDate,
            'chartPath' => $chartPath,
        ]);

        return $pdf->download("Отчёт_склада_{$warehouse->name}.pdf");
    }
}
