<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>{{ $title }}</h1>
<p>Data generacji raportu: {{ $date }}</p>

<table>
    <thead>
    <tr>
        <th>Pojazd</th>
        <th>Typ kosztu</th>
        <th>Kwota brutto</th>
        <th>Data</th>
        <th>Opis</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vehicleCosts as $cost)
        <tr>
            <td>{{ $cost->vehicle->brand }} - {{ $cost->vehicle->license_plate }}</td>
            <td>{{ $cost->costType->name }}</td>
            <td>{{ number_format($cost->amount_gross, 2) }} zł</td>
            <td>{{ $cost->date }}</td>
            <td>{{ $cost->description ?? 'Brak' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
