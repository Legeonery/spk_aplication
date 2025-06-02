<?php

namespace App\Http\Controllers;

use App\Models\WarehouseSparePart;
use App\Models\Warehouses;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SparePartMovement;

class SparePartReportController extends Controller
{
    public function generate($warehouseId)
    {
        $warehouse = Warehouses::findOrFail($warehouseId);

        $parts = WarehouseSparePart::with('sparePart')
            ->where('warehouse_id', $warehouseId)
            ->get();

        $movements = SparePartMovement::with('sparePart')
            ->where('warehouse_id', $warehouseId)
            ->orderBy('date')
            ->get();

        $pdf = Pdf::loadView('reports.spare_parts', [
            'warehouse' => $warehouse,
            'parts' => $parts,
            'movements' => $movements,
        ]);

        return $pdf->download("Отчет_по_складу_{$warehouse->name}.pdf");
    }
}
