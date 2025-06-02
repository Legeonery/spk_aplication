<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Отчёт по складу</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .chart {
            text-align: center;
            margin-bottom: 30px;
        }

        .summary {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h1>Отчёт по складу: {{ $warehouse->name }}</h1>
    <p><strong>Тип:</strong> {{ $warehouse->type }}</p>
    <p><strong>Площадь:</strong> {{ $warehouse->area }} м²</p>
    <p><strong>Макс. загрузка:</strong> {{ $warehouse->max_historical_load ?? '—' }} кг</p>
    <p><strong>Дата отчёта:</strong> {{ $reportDate }}</p>

    <h3>Остатки</h3>
    <table>
        <thead>
            <tr>
                <th>Культура</th>
                <th>Остаток (кг)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalStock = 0; @endphp
            @foreach($warehouse->grains as $grain)
                @php $totalStock += $grain->amount; @endphp
                <tr>
                    <td>{{ $grain->grainType->name ?? '—' }}</td>
                    <td>{{ number_format($grain->amount, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr class="summary">
                <td>Общий остаток:</td>
                <td>{{ number_format($totalStock, 2, ',', ' ') }} т</td>
            </tr>
        </tbody>
    </table>

    <h3>Поставки</h3>
    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Культура</th>
                <th>Объём (кг)</th>
                <th>Водитель</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDeliveries = 0; @endphp
            @foreach($warehouse->grainDeliveries as $d)
                @php $totalDeliveries += $d->volume; @endphp
                <tr>
                    <td>{{ $d->delivery_date }}</td>
                    <td>{{ $d->grainType->name ?? '—' }}</td>
                    <td>{{ number_format($d->volume, 2, ',', ' ') }}</td>
                    <td>{{ $d->driver->name ?? '—' }}</td>
                </tr>
            @endforeach
            <tr class="summary">
                <td colspan="2">Общий объём:</td>
                <td colspan="2">{{ number_format($totalDeliveries, 2, ',', ' ') }} кг</td>
            </tr>
        </tbody>
    </table>

    <h3>Отгрузки</h3>
    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Культура</th>
                <th>Объём (кг)</th>
                <th>Водитель</th>
            </tr>
        </thead>
        <tbody>
            @php $totalShipments = 0; @endphp
            @foreach($warehouse->grainShipments as $s)
                @php $totalShipments += $s->volume; @endphp
                <tr>
                    <td>{{ $s->shipment_date }}</td>
                    <td>{{ $s->grainType->name ?? '—' }}</td>
                    <td>{{ number_format($s->volume, 2, ',', ' ') }}</td>
                    <td>{{ $s->driver->name ?? '—' }}</td>
                </tr>
            @endforeach
            <tr class="summary">
                <td colspan="2">Общий объём:</td>
                <td colspan="2">{{ number_format($totalShipments, 2, ',', ' ') }} кг</td>
            </tr>
        </tbody>
    </table>

    @if(isset($chartPath) && file_exists(public_path($chartPath)))
        <h3>Диаграмма активности</h3>
        <div class="chart">
            <img src="{{ public_path($chartPath) }}" alt="Диаграмма поставок и отгрузок" style="width:100%;">
        </div>
    @endif
</body>

</html>