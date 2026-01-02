<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .title {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .period {
        font-size: 12px;
        margin-bottom: 6px;
    }

    .divider {
        border-top: 2px solid #000;
        margin-bottom: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    thead th {
        padding: 4px 6px;
        text-align: left;
        border-bottom: 2px solid #000;
        font-weight: bold;
    }

    tbody td {
        padding: 4px 6px;
        height: 18px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    tr.row-even td { background: #f0f0f0; }
    tr.row-odd  td { background: #ffffff; }

    tr.spacer-row td {
        background: transparent !important;
        height: 18px;
    }

    tr { page-break-inside: avoid; }

    .footer {
        margin-top: 30px;
        border-top: 2px solid #000;
        padding-top: 8px;
        font-size: 12px;
    }

    .footer-table {
        width: 100%;
        border-collapse: collapse;
    }

    .footer-left  { text-align: left; }
    .footer-right { text-align: right; }

    .footer-logo { height: 55px; }
</style>
</head>

<body>

@php
    $rowCount = 0;
    $page = $pageStart;
    $perPage = 20;
@endphp

<div class="title">
    {{ $typeReport === 'daily' ? 'Daily Report' : 'Monthly Report' }}
</div>

@if ($typeReport === 'daily' && $startDate)
<div class="period">
    Reporting Date:
    <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
</div>
@endif

@if ($typeReport === 'monthly' && $startDate && $endDate)
<div class="period">
    Reporting Period:
    <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
    through
    <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
</div>
@endif

<div class="divider"></div>

<table>
<thead>
    <tr>
    @if ($typeReport === 'daily')
        <th>Date / Time</th>
        <th>Transaction</th>
        <th>Tenant Name</th>
        <th>Door Name</th>
        <th>Full Name</th>
        <th>Location</th>
    @else
        <th>Date / Time</th>
        <th>Full Name</th>
        <th>Card Number</th>
        <th>Expired</th>
        <th>Location</th>
    @endif
    </tr>
</thead>
<tbody>

@foreach ($reports as $row)

@if ($rowCount > 0 && $rowCount % $perPage === 0)

    </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Printed:
                    <strong>{{ now()->format('d/m/Y H:i:s') }}</strong><br>
                    Page {{ $page }} of {{ $totalPageGlobal }}
                </td>
                <td class="footer-right">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" class="footer-logo">
                </td>
            </tr>
        </table>
    </div>

    <div style="page-break-after: always;"></div>

    @php $page++; @endphp

    <div class="title">
        {{ $typeReport === 'daily' ? 'Daily Report' : 'Monthly Report' }}
    </div>

    @if ($typeReport === 'daily' && $startDate)
    <div class="period">
        Reporting Date:
        <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
    </div>
    @endif

    @if ($typeReport === 'monthly' && $startDate && $endDate)
    <div class="period">
        Reporting Period:
        <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
        through
        <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
    </div>
    @endif

    <div class="divider"></div>

    <table>
    <thead>
        <tr>
        @if ($typeReport === 'daily')
            <th>Date / Time</th>
            <th>Transaction</th>
            <th>Tenant Name</th>
            <th>Door Name</th>
            <th>Full Name</th>
            <th>Location</th>
        @else
            <th>Date / Time</th>
            <th>Full Name</th>
            <th>Card Number</th>
            <th>Expired</th>
            <th>Location</th>
        @endif
        </tr>
    </thead>
    <tbody>
@endif

<tr class="{{ $rowCount % 2 === 0 ? 'row-even' : 'row-odd' }}">
@if ($typeReport === 'daily')
    <td>{{ \Carbon\Carbon::parse($row->ts)->format('d-M-Y H:i:s') }}</td>
    <td>{{ $row->msgdescription }}</td>
    <td>{{ $row->credential?->owner?->group?->description ?? '-' }}</td>
    <td>{{ Str::between($row->hardwaredescription, '[', ']') ?? '-' }}</td>
    <td>{{ $row->credential?->owner?->description ?? '-' }}</td>
    <td>{{ $row->partition?->description ?? '-' }}</td>
@else
    <td>{{ \Carbon\Carbon::parse($row->ts)->format('d-M-Y H:i:s') }}</td>
    <td>{{ $row->credential?->owner?->description ?? '-' }}</td>
    <td>{{ $row->credential?->cardnumber ?? '-' }}</td>
    <td>{{ $row->credential?->enddate ?? '-' }}</td>
    <td>{{ $row->partition?->description ?? '-' }}</td>
@endif
</tr>

@php $rowCount++; @endphp
@endforeach

@php
    $rowsLastPage = $rowCount % $perPage ?: $perPage;
    $emptyRows = $perPage - $rowsLastPage;
@endphp

@for ($i = 0; $i < $emptyRows; $i++)
<tr class="spacer-row">
@if ($typeReport === 'daily')
    <td colspan="4">&nbsp;</td>
@else
    <td colspan="5">&nbsp;</td>
@endif
</tr>
@endfor

</tbody>
</table>

<div class="footer">
<table class="footer-table">
<tr>
    <td class="footer-left">
        Printed:
        <strong>{{ now()->format('d/m/Y H:i:s') }}</strong><br>
        Page {{ $page }} of {{ $totalPageGlobal }}
    </td>
    <td class="footer-right">
        <img src="data:image/png;base64,{{ $logoBase64 }}" class="footer-logo">
    </td>
</tr>
</table>
</div>

</body>
</html>