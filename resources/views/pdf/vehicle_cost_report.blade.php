@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz@9..40&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,500&display=swap" rel="stylesheet">
    <title>{{ $title }}</title>
    <style>
        html {
            margin: 0;
            padding: 0;
        }

        table,
        caption,
        tbody,
        tfoot,
        thead,
        tr,
        th,
        td {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
            color: #2a2a2a;
            text-align: left;
        }

        table {
            border-spacing: 0;
            table-layout: auto;
            color: #2a2a2a;
        }

        body {
            font-family: "DejaVu Sans Mono", monospace;
            font-weight: 400;
            padding: 0;
            color: #2a2a2a;
        }

        .container {
            padding: 24px 24px;
        }
    </style>
</head>
<body>
<table style="margin: 12px 0 8px 24px; border-bottom: 1px solid #ddd; width: calc(100% - 48px)">
    <tr style="height: 14px;">
        <td style="text-align: left; height: 14px;">
            <p style="font-size: 14px; color: #0D47A1; margin: 0;">Carfleet</p>
        </td>
    </tr>
</table>
<div class="container">
    <h1 style="font-size: 14pt; margin: 0">{{ $title }}</h1>
    <p style="font-size: 10pt; margin: 0 0 24px;">Data generacji raportu: {{ $date }}</p>
    @if($startDate || $endDate)
        <p style="font-size: 14px">Zakres dat:
            @if($startDate) od <b>{{ Carbon::parse($startDate)->format('d.m.Y') }}</b> @endif
            @if($endDate)do <b>{{ Carbon::parse($endDate)->format('d.m.Y') }}</b> @endif
        </p>
    @endif

    <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #4a4a4a;">
        <thead>
            <tr style="background-color: #f9f9f9; color: #333; text-align: left; border-bottom: 2px solid #ddd;">
                <th style="padding: 8px;">Data</th>
                <th style="padding: 8px;">Pojazd</th>
                <th style="padding: 8px;">Rejestracja</th>
                <th style="padding: 8px;">Rodzaj kosztu</th>
                <th style="padding: 8px;">Opis</th>
                <th style="padding: 8px; text-align: right;">Kwota netto</th>
                <th style="padding: 8px; text-align: right;">VAT</th>
                <th style="padding: 8px; text-align: right;">Kwota brutto</th>
            </tr>
        </thead>
        <tbody>
        @foreach($vehicleCosts as $cost)
            <tr style="border-bottom: 1px solid #ddd; transition: background-color 0.3s;">
                <td style="padding: 8px;">{{ Carbon::parse($cost->date)->format('d.m.Y') }}</td>
                <td style="padding: 8px;">{{ $cost->vehicle->brand ?? 'N/A' }}</td>
                <td style="padding: 8px;">{{ $cost->vehicle->license_plate }}</td>
                <td style="padding: 8px;">{{ $cost->cost_type_name }}</td>
                <td style="padding: 8px;">{{ $cost->description }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($cost->amount_net, 2) }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($cost->vat_amount, 2) }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($cost->amount_gross, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f1f1; font-weight: bold; border-top: 2px solid #ddd;">
                <td colspan="5" style="padding: 8px; text-align: left;">Razem</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($totalNet, 2) }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($totalVat, 2) }}</td>
                <td style="padding: 8px; text-align: right;">{{ number_format($totalGross, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
