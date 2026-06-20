<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Service Sticker Label - {{ $record->car->car_plate_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* 80mm x 50mm standard sticker dimensions approx */
        .sticker-card {
            width: 320px;
            height: 200px;
            border: 2px dashed #000000;
            border-radius: 8px;
            padding: 12px;
            box-sizing: border-box;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sticker-header {
            text-align: center;
            border-bottom: 1px solid #000000;
            padding-bottom: 4px;
        }

        .sticker-title {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sticker-subtitle {
            font-size: 10px;
            margin: 2px 0 0 0;
            color: #555;
        }

        .sticker-body {
            margin: 8px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .info-row strong {
            text-transform: uppercase;
        }

        .highlight-box {
            border: 1px solid #000000;
            background-color: #f8fafc;
            padding: 6px;
            text-align: center;
            margin-top: 4px;
        }

        .highlight-title {
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .highlight-value {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .sticker-footer {
            font-size: 8px;
            text-align: center;
            color: #777;
            border-top: 1px dotted #000000;
            padding-top: 4px;
        }

        @media print {
            body {
                background-color: transparent;
                height: auto;
            }
            .sticker-card {
                border: 2px solid #000000; /* Solid border for actual printing */
                page-break-inside: avoid;
            }
            .no-print {
                display: none;
            }
        }
        
        .print-btn-container {
            position: fixed;
            top: 10px;
            right: 10px;
        }

        .btn-print {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .btn-print:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button class="btn-print" onclick="window.print()"><i class="fa fas fa-print"></i> Print Label</button>
    </div>

    <div class="sticker-card">
        <div class="sticker-header">
            <h2 class="sticker-title">{{ Session::get('business.name') }}</h2>
            <p class="sticker-subtitle">MAINTENANCE RECORD STICKER</p>
        </div>

        <div class="sticker-body">
            <div class="info-row">
                <span>PLATE NO:</span>
                <strong>{{ strtoupper($record->car->car_plate_number) }}</strong>
            </div>
            
            <div class="info-row">
                <span>SERVICE DATE:</span>
                <strong>{{ $record->service_date ? $record->service_date->format('Y-m-d') : date('Y-m-d') }}</strong>
            </div>

            @if($record->service_type !== 'spares')
                <div class="info-row">
                    <span>SERVICED AT:</span>
                    <strong>{{ number_format($record->serviced_kilometer) }} KM</strong>
                </div>

                <div class="highlight-box">
                    <h3 class="highlight-title">Next Service Mileage At</h3>
                    <p class="highlight-value">{{ number_format($record->next_service_kilometer) }} KM</p>
                </div>
            @else
                <div class="highlight-box" style="margin-top: 10px;">
                    <h3 class="highlight-title">SERVICE TYPE</h3>
                    <p class="highlight-value" style="font-size: 13px; text-transform: uppercase;">SPARE PARTS CHANGED</p>
                </div>
            @endif
        </div>

        <div class="sticker-footer">
            Thank you for your business! Drive safely.
        </div>
    </div>

    <script type="text/javascript">
        window.onload = function() {
            // Auto open print dialog
            window.print();
        };
    </script>

</body>
</html>
