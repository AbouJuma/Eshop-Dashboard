@php
    /** @var \App\Models\CarMaintenance $record */
    $car = $record->car;
    $isCard = $doc === 'card';
    $narrow = in_array($size, ['58mm', '80mm', 'label']);
    $pageSize = [
        '58mm' => '58mm auto',
        '80mm' => '80mm auto',
        'label' => '80mm 50mm',
        'a5' => 'A5',
        'a4' => 'A4',
    ][$size];
    $businessName = $business->name ?? 'DADIS ONE STOP';
    $address = $location ? collect([$location->landmark, $location->city])->filter()->implode(', ') : null;
    $phone = $location->mobile ?? null;
    $email = $location->email ?? null;
    $lines = collect(preg_split('/\r\n|\r|\n/', (string) $record->details))->map(fn ($l) => trim($l, " \t-•*"))->filter()->values();
    $vehicle = trim(implode(' ', array_filter([$car->car_brand ?? null, $car->car_model ?? null, $car->car_year ?? null])));
    $money = fn ($v) => 'TZS '.number_format((float) $v, 0);
    $title = ($isCard ? 'Next Service Card' : 'Service Receipt').' · '.($car->car_plate_number ?? $record->receipt_number);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        @page { size: {{ $pageSize }}; margin: {{ $size === 'label' ? '0' : ($narrow ? '2mm 0' : '12mm') }}; }

        :root { --ink: #111; --muted: #555; --line: #111; --accent: #ea580c; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #e5e7eb; color: var(--ink); }
        body { font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        /* ---------- toolbar (screen only) ---------- */
        .toolbar { position: sticky; top: 0; z-index: 5; background: #0f172a; color: #fff; padding: 10px 16px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: center; font-size: 14px; }
        .toolbar .seg { display: inline-flex; background: #1e293b; border-radius: 8px; padding: 3px; }
        .toolbar .seg a { color: #cbd5e1; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; white-space: nowrap; }
        .toolbar .seg a.on { background: #fff; color: #0f172a; }
        .toolbar select { background: #1e293b; color: #fff; border: 1px solid #334155; border-radius: 8px; padding: 7px 10px; font-size: 14px; }
        .toolbar button { background: var(--accent); color: #fff; border: 0; border-radius: 8px; padding: 9px 18px; font-weight: 700; font-size: 14px; cursor: pointer; }
        .toolbar .back { color: #94a3b8; text-decoration: none; }
        .stage { padding: 24px 12px 48px; display: flex; justify-content: center; }

        /* ---------- paper ---------- */
        .paper { background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,.15); }
        .size-58mm .paper { width: 58mm; padding: 3mm 2.5mm; font-size: 10.5px; }
        .size-80mm .paper { width: 80mm; padding: 4mm 4mm; font-size: 12px; }
        .size-label .paper { width: 80mm; height: 50mm; padding: 2.5mm 3mm; font-size: 10px; overflow: hidden; }
        .size-a5 .paper { width: 148mm; min-height: 210mm; padding: 12mm; font-size: 12.5px; }
        .size-a4 .paper { width: 210mm; min-height: 297mm; padding: 16mm; font-size: 13.5px; }

        .center { text-align: center; }
        .muted { color: var(--muted); }
        .upper { text-transform: uppercase; letter-spacing: .04em; }
        .b { font-weight: 700; }
        .mono { font-family: "Courier New", Courier, monospace; }
        .rule { border: 0; border-top: 1px dashed var(--line); margin: 2.5mm 0; }
        .rule.solid { border-top-style: solid; }
        .row { display: flex; justify-content: space-between; gap: 6px; }
        .row > :last-child { text-align: right; }
        .logo { max-height: 14mm; max-width: 60%; object-fit: contain; }
        .size-58mm .logo { max-height: 10mm; }
        h1, h2, h3, p { margin: 0; }
        .biz { font-weight: 800; font-size: 1.25em; line-height: 1.15; }
        .doc-title { display: inline-block; margin-top: 1.5mm; padding: .8mm 3mm; border: 1.5px solid var(--ink); border-radius: 999px; font-weight: 800; font-size: .95em; letter-spacing: .08em; }

        /* ---------- receipt (narrow) ---------- */
        .items { width: 100%; border-collapse: collapse; }
        .items td { padding: 1mm 0; vertical-align: top; }
        .items td.amt { text-align: right; white-space: nowrap; }
        .total { font-size: 1.35em; font-weight: 800; }
        .next-box { border: 1.5px solid var(--ink); border-radius: 2mm; padding: 2mm; text-align: center; margin-top: 2.5mm; }
        .next-box .v { font-size: 1.5em; font-weight: 800; }

        /* ---------- receipt (A4 / A5) ---------- */
        .sheet-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10mm; padding-bottom: 6mm; border-bottom: 3px solid var(--ink); }
        .sheet-head .logo { max-height: 20mm; max-width: 50mm; }
        .sheet-head .doc { text-align: right; }
        .sheet-head .doc h2 { font-size: 1.9em; font-weight: 800; letter-spacing: .06em; }
        .accent-bar { height: 2mm; background: var(--accent); margin: 0 0 6mm; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 6mm; margin: 6mm 0; }
        .panel { border: 1px solid #d1d5db; border-radius: 2mm; padding: 4mm; }
        .panel h3 { font-size: .8em; color: var(--muted); letter-spacing: .1em; text-transform: uppercase; margin-bottom: 2mm; }
        .kv { display: grid; grid-template-columns: auto 1fr; gap: 1mm 4mm; }
        .kv span:nth-child(odd) { color: var(--muted); }
        .table { width: 100%; border-collapse: collapse; margin-top: 2mm; }
        .table th { text-align: left; font-size: .8em; letter-spacing: .08em; text-transform: uppercase; background: #111; color: #fff; padding: 2.5mm 3mm; }
        .table td { padding: 2.5mm 3mm; border-bottom: 1px solid #e5e7eb; }
        .table .amt { text-align: right; white-space: nowrap; }
        .totals { margin-left: auto; width: 60%; margin-top: 4mm; }
        .totals .row { padding: 2mm 3mm; }
        .totals .grand { background: #111; color: #fff; font-size: 1.25em; font-weight: 800; border-radius: 1.5mm; }
        .sign { display: grid; grid-template-columns: 1fr 1fr; gap: 14mm; margin-top: 18mm; }
        .sign div { border-top: 1px solid var(--ink); padding-top: 2mm; font-size: .85em; color: var(--muted); }
        .foot { margin-top: 10mm; text-align: center; color: var(--muted); font-size: .85em; }

        /* ---------- next service card ---------- */
        .card-head { display: flex; align-items: center; justify-content: space-between; gap: 2mm; border-bottom: 1.5px solid var(--ink); padding-bottom: 1.2mm; }
        .card-head .biz { font-size: 1.05em; }
        .card-head .logo { max-height: 8mm; max-width: 22mm; }
        .due { text-align: center; margin: 1.6mm 0; }
        .due .lbl { font-size: .8em; font-weight: 800; letter-spacing: .14em; }
        .due .km { font-size: 2.3em; font-weight: 900; line-height: 1.05; letter-spacing: -.01em; }
        .due .or { font-size: .95em; font-weight: 700; }
        .plate { display: inline-block; border: 1.5px solid var(--ink); border-radius: 1mm; padding: .3mm 2mm; font-weight: 900; letter-spacing: .08em; }
        .card-foot { display: flex; justify-content: space-between; border-top: 1px dashed var(--ink); padding-top: 1mm; font-size: .85em; }
        .size-a4 .card-wrap, .size-a5 .card-wrap { border: 2px solid var(--ink); border-radius: 4mm; padding: 8mm; }
        .size-a4 .due .km, .size-a5 .due .km { font-size: 4em; }
        .size-80mm .due .km { font-size: 2.6em; }
        .size-58mm .due .km { font-size: 2em; }
        .cut { text-align: center; color: var(--muted); font-size: .8em; margin-top: 2mm; }

        @media print {
            html, body { background: #fff; }
            .toolbar { display: none !important; }
            .stage { padding: 0; display: block; }
            .paper { box-shadow: none; margin: 0 auto; }
            .size-a4 .paper, .size-a5 .paper { width: auto; min-height: 0; padding: 0; }
            .cut { display: none; }
        }
        @media screen and (max-width: 640px) {
            .size-a4 .paper, .size-a5 .paper { width: 100%; min-height: 0; padding: 6mm; }
            .grid2 { grid-template-columns: 1fr; }
            .totals { width: 100%; }
        }
    </style>
</head>
<body class="size-{{ $size }}">

<div class="toolbar">
    @unless($shared)
        <a class="back" href="{{ route('car-maintenance.index') }}">← Back</a>
    @endunless
    <div class="seg">
        <a href="{{ request()->fullUrlWithQuery(['doc' => 'card', 'size' => in_array($size, ['80mm', '58mm']) ? $size : 'label']) }}" class="{{ $isCard ? 'on' : '' }}">Next service card</a>
        <a href="{{ request()->fullUrlWithQuery(['doc' => 'receipt', 'size' => $size === 'label' ? '80mm' : $size]) }}" class="{{ $isCard ? '' : 'on' }}">Receipt</a>
    </div>
    <select id="size" aria-label="Paper size">
        @foreach($sizes as $key => $label)
            @if(! ($key === 'label' && ! $isCard))
                <option value="{{ $key }}" {{ $key === $size ? 'selected' : '' }}>{{ $label }}</option>
            @endif
        @endforeach
    </select>
    <button type="button" onclick="window.print()">🖨 Print</button>
</div>

<div class="stage">
<div class="paper">

@if($isCard)
    {{-- ================= NEXT SERVICE CARD ================= --}}
    <div class="card-wrap">
        <div class="card-head">
            <div>
                <div class="biz upper">{{ $businessName }}</div>
                @if($phone)<div class="muted">☎ {{ $phone }}</div>@endif
            </div>
            @if($logo && $size !== '58mm')<img class="logo" src="{{ $logo }}" alt="">@endif
        </div>

        <div class="due">
            <div class="lbl">NEXT SERVICE DUE</div>
            @if($record->next_service_kilometer)
                <div class="km">{{ number_format($record->next_service_kilometer) }} km</div>
            @endif
            @if($record->next_service_date)
                <div class="{{ $record->next_service_kilometer ? 'or' : 'km' }}">
                    {{ $record->next_service_kilometer ? 'or by ' : '' }}{{ $record->next_service_date->format('d M Y') }}
                </div>
                @if($record->next_service_kilometer)<div class="muted" style="font-size:.8em">whichever comes first</div>@endif
            @endif
            @if(! $record->hasNextService())
                <div class="or muted">Not scheduled</div>
            @endif
        </div>

        <div class="row" style="align-items:center">
            <span class="plate">{{ strtoupper($car->car_plate_number ?? '—') }}</span>
            <span class="b">{{ $record->type_label }}</span>
        </div>
        <div class="card-foot" style="margin-top:1.2mm">
            <span>Serviced {{ optional($record->service_date)->format('d/m/Y') }}</span>
            @if($record->serviced_kilometer)<span>at {{ number_format($record->serviced_kilometer) }} km</span>@endif
        </div>
        @if(in_array($size, ['a4', 'a5', '80mm']))
            <hr class="rule">
            <p class="center muted">Please return for your next service on time.<br>Thank you for choosing {{ $businessName }}.</p>
        @endif
    </div>
    @if($size === 'label')<div class="cut">Label 80 × 50 mm — choose your label printer in the print dialog</div>@endif

@elseif($narrow)
    {{-- ================= RECEIPT — THERMAL ================= --}}
    <div class="center">
        @if($logo)<img class="logo" src="{{ $logo }}" alt=""><br>@endif
        <div class="biz upper">{{ $businessName }}</div>
        @if($address)<div class="muted">{{ $address }}</div>@endif
        @if($phone)<div class="muted">Tel: {{ $phone }}</div>@endif
        @if($email && $size === '80mm')<div class="muted">{{ $email }}</div>@endif
        <div class="doc-title">SERVICE RECEIPT</div>
    </div>
    <hr class="rule">
    <div class="row"><span>Receipt No</span><span class="b mono">{{ $record->receipt_number }}</span></div>
    <div class="row"><span>Date</span><span>{{ optional($record->service_date)->format('d/m/Y') }}</span></div>
    <div class="row"><span>Service</span><span class="b">{{ $record->type_label }}</span></div>
    <hr class="rule">
    <div class="row"><span>Plate No</span><span class="b">{{ strtoupper($car->car_plate_number ?? '—') }}</span></div>
    @if($vehicle)<div class="row"><span>Vehicle</span><span>{{ $vehicle }}</span></div>@endif
    @if($car && $car->owner_name)<div class="row"><span>Customer</span><span>{{ $car->owner_name }}</span></div>@endif
    @if($car && $car->owner_phone)<div class="row"><span>Phone</span><span>{{ $car->owner_phone }}</span></div>@endif
    @if($record->serviced_kilometer)<div class="row"><span>Mileage</span><span>{{ number_format($record->serviced_kilometer) }} km</span></div>@endif
    <hr class="rule">
    <div class="b upper" style="margin-bottom:1mm">Work done</div>
    <table class="items">
        @forelse($lines as $line)
            <tr><td>• {{ $line }}</td></tr>
        @empty
            <tr><td>{{ $record->type_label }}</td></tr>
        @endforelse
    </table>
    <hr class="rule solid">
    <div class="row total"><span>TOTAL</span><span>{{ $money($record->cost) }}</span></div>
    <hr class="rule solid">
    @if($record->hasNextService())
        <div class="next-box">
            <div class="b upper" style="font-size:.85em">Next service</div>
            @if($record->next_service_kilometer)<div class="v">{{ number_format($record->next_service_kilometer) }} km</div>@endif
            @if($record->next_service_date)<div class="b">{{ $record->next_service_kilometer ? 'or ' : '' }}{{ $record->next_service_date->format('d M Y') }}</div>@endif
        </div>
    @endif
    @if($record->creator)<div class="center muted" style="margin-top:2mm">Served by {{ trim($record->creator->first_name.' '.$record->creator->last_name) }}</div>@endif
    <div class="center" style="margin-top:2.5mm">
        <div class="b">Thank you for your business!</div>
        <div class="muted">Drive safely.</div>
        <div class="muted mono" style="margin-top:1.5mm; font-size:.85em">{{ now()->format('d/m/Y H:i') }}</div>
    </div>

@else
    {{-- ================= RECEIPT — A4 / A5 ================= --}}
    <div class="sheet-head">
        <div>
            @if($logo)<img class="logo" src="{{ $logo }}" alt="" style="margin-bottom:3mm"><br>@endif
            <div class="biz upper">{{ $businessName }}</div>
            @if($address)<div class="muted">{{ $address }}</div>@endif
            @if($phone)<div class="muted">Tel: {{ $phone }}</div>@endif
            @if($email)<div class="muted">{{ $email }}</div>@endif
        </div>
        <div class="doc">
            <h2>RECEIPT</h2>
            <div class="muted upper" style="font-size:.85em">{{ $record->type_label }}</div>
            <div style="margin-top:3mm" class="kv">
                <span>Receipt No</span><span class="b mono">{{ $record->receipt_number }}</span>
                <span>Date</span><span>{{ optional($record->service_date)->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    <div class="accent-bar"></div>

    <div class="grid2">
        <div class="panel">
            <h3>Customer</h3>
            <div class="kv">
                <span>Name</span><span class="b">{{ $car->owner_name ?? '—' }}</span>
                @if($car && $car->owner_phone)<span>Phone</span><span>{{ $car->owner_phone }}</span>@endif
                @if($car && $car->owner_email)<span>Email</span><span>{{ $car->owner_email }}</span>@endif
            </div>
        </div>
        <div class="panel">
            <h3>Vehicle</h3>
            <div class="kv">
                <span>Plate No</span><span class="b">{{ strtoupper($car->car_plate_number ?? '—') }}</span>
                @if($vehicle)<span>Make / model</span><span>{{ $vehicle }}</span>@endif
                @if($car && $car->car_chassis_number)<span>Chassis</span><span class="mono">{{ $car->car_chassis_number }}</span>@endif
                @if($record->serviced_kilometer)<span>Mileage</span><span>{{ number_format($record->serviced_kilometer) }} km</span>@endif
            </div>
        </div>
    </div>

    <table class="table">
        <thead><tr><th style="width:10mm">#</th><th>Description of work</th><th class="amt">Amount</th></tr></thead>
        <tbody>
        @forelse($lines as $i => $line)
            <tr><td>{{ $i + 1 }}</td><td>{{ $line }}</td><td class="amt">{{ $i === 0 && $lines->count() === 1 ? $money($record->cost) : '' }}</td></tr>
        @empty
            <tr><td>1</td><td>{{ $record->type_label }}</td><td class="amt">{{ $money($record->cost) }}</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="row grand"><span>TOTAL</span><span>{{ $money($record->cost) }}</span></div>
    </div>

    @if($record->hasNextService())
        <div class="panel" style="margin-top:8mm; display:flex; justify-content:space-between; align-items:center">
            <div><h3 style="margin:0">Next service due</h3><span class="muted">Whichever comes first</span></div>
            <div class="b" style="font-size:1.3em; text-align:right">
                @if($record->next_service_kilometer){{ number_format($record->next_service_kilometer) }} km @endif
                @if($record->next_service_kilometer && $record->next_service_date) · @endif
                @if($record->next_service_date){{ $record->next_service_date->format('d M Y') }}@endif
            </div>
        </div>
    @endif

    <div class="sign">
        <div>Served by{{ $record->creator ? ': '.trim($record->creator->first_name.' '.$record->creator->last_name) : '' }}</div>
        <div>Customer signature</div>
    </div>
    <div class="foot">Thank you for choosing {{ $businessName }}. Drive safely!</div>
@endif

</div>
</div>

<script>
    @if(in_array($size, ['58mm', '80mm']))
    // Roll paper: browsers ignore "auto" page height, so size the page to the receipt once it has rendered.
    window.addEventListener('load', function () {
        var paper = document.querySelector('.paper');
        var mm = Math.ceil(paper.getBoundingClientRect().height * 25.4 / 96) + 6;
        var style = document.createElement('style');
        style.textContent = '@page { size: {{ $size }} ' + mm + 'mm; margin: 2mm 0; }';
        document.head.appendChild(style);
    });
    @endif
    (function () {
        var key = 'dadis.print.{{ $doc }}';
        var select = document.getElementById('size');
        select.addEventListener('change', function () {
            try { localStorage.setItem(key, select.value); } catch (e) {}
            var url = new URL(window.location.href);
            url.searchParams.set('size', select.value);
            window.location.href = url.toString();
        });
        @unless($explicitSize)
        // reuse the paper size chosen last time for this document
        try {
            var saved = localStorage.getItem(key);
            if (saved && saved !== '{{ $size }}' && select.querySelector('option[value="' + saved + '"]')) {
                var url = new URL(window.location.href);
                url.searchParams.set('size', saved);
                window.location.replace(url.toString());
            }
        } catch (e) {}
        @endunless
    })();
</script>
</body>
</html>
