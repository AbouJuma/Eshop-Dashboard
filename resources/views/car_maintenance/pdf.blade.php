<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Car Maintenance Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header-container {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .meta-table td {
            padding: 2px 0;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-data th {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .table-data td {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 10px;
            vertical-align: top;
        }

        .table-data tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .badge {
            font-size: 9px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-normal {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-spares {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-both {
            background-color: #f3e8ff;
            color: #5b21b6;
        }

        .text-right {
            text-align: right;
        }

        .summary-container {
            margin-top: 30px;
            border-top: 1px solid #cbd5e1;
            padding-top: 15px;
            text-align: right;
        }

        .summary-box {
            display: inline-block;
            width: 250px;
            text-align: left;
        }

        .summary-row {
            font-size: 12px;
            padding: 3px 0;
        }

        .summary-value {
            font-weight: bold;
            float: right;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="report-title">Car Maintenance Report</h1>
                    <p class="report-subtitle">Generated on {{ date('Y-m-d H:i') }}</p>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    <strong style="color: #3b82f6; font-size: 14px;">{{ Session::get('business.name') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Active Filters Description -->
    <table class="meta-table">
        <tr>
            <td style="width: 12%;"><strong>Filters Applied:</strong></td>
            <td>
                @php
                    $filters = [];
                    if($request->filled('car_plate_number')) $filters[] = "Plate Number: " . strtoupper($request->car_plate_number);
                    if($request->filled('owner_name')) $filters[] = "Owner: " . $request->owner_name;
                    if($request->filled('service_type')) $filters[] = "Type: " . ucfirst($request->service_type);
                    if($request->filled('start_date') && $request->filled('end_date')) $filters[] = "Date: " . $request->start_date . " to " . $request->end_date;
                @endphp
                {{ !empty($filters) ? implode(' | ', $filters) : 'General Report (All Records)' }}
            </td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 12%;">Plate No</th>
                <th style="width: 20%;">Vehicle Info</th>
                <th style="width: 15%;">Owner</th>
                <th style="width: 10%;">Mileage</th>
                <th style="width: 12%;">Next Service</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 11%;" class="text-right">Cost</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCost = 0; @endphp
            @forelse($records as $item)
                @php $totalCost += $item->cost; @endphp
                <tr>
                    <td>{{ $item->service_date ? $item->service_date->format('Y-m-d') : 'N/A' }}</td>
                    <td><strong>{{ strtoupper($item->car->car_plate_number) }}</strong></td>
                    <td>
                        {{ $item->car->car_brand }} {{ $item->car->car_model }}
                        @if($item->car->car_year) ({{ $item->car->car_year }}) @endif
                        @if($item->details)
                            <div style="font-size: 9px; color: #64748b; margin-top: 4px; font-style: italic;">
                                {{ Str::limit($item->details, 60) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ $item->car->owner_name }}
                        @if($item->car->owner_phone) <br><span style="font-size: 9px; color: #64748b;">{{ $item->car->owner_phone }}</span> @endif
                    </td>
                    <td>
                        @if($item->service_type == 'spares' && is_null($item->serviced_kilometer))
                            N/A
                        @else
                            {{ number_format($item->serviced_kilometer) }} km
                        @endif
                    </td>
                    <td>
                        @if($item->service_type == 'spares' && is_null($item->next_service_kilometer))
                            N/A
                        @else
                            {{ number_format($item->next_service_kilometer) }} km
                        @endif
                    </td>
                    <td>
                        @if($item->service_type == 'normal')
                            <span class="badge badge-normal">Normal</span>
                        @elseif($item->service_type == 'spares')
                            <span class="badge badge-spares">Spares</span>
                        @else
                            <span class="badge badge-both">Both</span>
                        @endif
                    </td>
                    <td class="text-right"><strong>{{ number_format($item->cost, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No maintenance records found matching filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-container">
        <div class="summary-box">
            <div class="summary-row">
                Total Records:
                <span class="summary-value">{{ count($records) }}</span>
            </div>
            <div class="summary-row" style="font-size: 14px; margin-top: 5px; border-top: 1px solid #e2e8f0; padding-top: 5px;">
                Total Cost:
                <span class="summary-value" style="color: #059669;">{{ number_format($totalCost, 2) }}</span>
            </div>
        </div>
    </div>

</body>
</html>
