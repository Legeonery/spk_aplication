<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Отчёт по складу {{ $warehouse->name }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background-color: #eee; }
    h2, h3 { margin-top: 20px; }
  </style>
</head>
<body>
  <h2>Отчёт по складу: {{ $warehouse->name }}</h2>
  <p><strong>Тип:</strong> {{ $warehouse->type }}</p>
  <p><strong>Площадь:</strong> {{ $warehouse->area }} м²</p>

  <h3>Остатки запчастей</h3>
  <table>
    <thead>
      <tr>
        <th>Название</th>
        <th>Артикул</th>
        <th>Количество</th>
      </tr>
    </thead>
    <tbody>
      @foreach($parts as $item)
        <tr>
          <td>{{ $item->sparePart->name }}</td>
          <td>{{ $item->sparePart->article }}</td>
          <td>{{ $item->quantity }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h3>История движений (поступления и списания)</h3>
  <table>
    <thead>
      <tr>
        <th>Дата</th>
        <th>Тип</th>
        <th>Название</th>
        <th>Артикул</th>
        <th>Количество</th>
        <th>Причина</th>
      </tr>
    </thead>
    <tbody>
      @foreach($movements as $move)
        <tr>
          <td>{{ $move->date }}</td>
          <td>{{ $move->type === 'in' ? 'Поступление' : 'Списание' }}</td>
          <td>{{ $move->sparePart->name }}</td>
          <td>{{ $move->sparePart->article ?? '—' }}</td>
          <td>{{ $move->quantity }}</td>
          <td>{{ $move->reason ?? '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
