{{-- Service part of a maintenance record, shared by create and edit. $maintenance is null when creating. --}}
@php
    $m = $maintenance ?? null;
    $currentType = old('service_type', $m->service_type ?? 'oil_change');
    $typeHelp = [
        'oil_change' => 'Prints a next-service card (sticker) for the windscreen.',
        'periodic_maintenance' => 'Prints a company receipt.',
        'repair' => 'Prints a company receipt.',
    ];
@endphp
<div class="form-group">
    <label>Service category <span class="text-danger">*</span></label>
    <div class="service-type-cards">
        @foreach(\App\Models\CarMaintenance::TYPES as $value => $label)
            <label class="service-type-card {{ $currentType === $value ? 'active' : '' }}">
                <input type="radio" name="service_type" value="{{ $value }}" {{ $currentType === $value ? 'checked' : '' }} required>
                <i class="fa fas {{ ['oil_change' => 'fa-oil-can', 'periodic_maintenance' => 'fa-calendar-check', 'repair' => 'fa-wrench'][$value] }}"></i>
                <strong>{{ $label }}</strong>
                <small>{{ $typeHelp[$value] }}</small>
            </label>
        @endforeach
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="service_date">Service Date <span class="text-danger">*</span></label>
            <input type="date" name="service_date" id="service_date" class="form-control" value="{{ old('service_date', $m && $m->service_date ? $m->service_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="cost">Amount charged (TZS)</label>
            <input type="number" step="0.01" min="0" name="cost" id="cost" class="form-control" value="{{ old('cost', $m->cost ?? '') }}" placeholder="0.00">
        </div>
    </div>
</div>

<div id="mileage-section">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="serviced_kilometer">Current Mileage (km) <span class="text-danger km-required">*</span><small class="text-muted km-optional"> (optional)</small></label>
                <input type="number" min="0" name="serviced_kilometer" id="serviced_kilometer" class="form-control" value="{{ old('serviced_kilometer', $m->serviced_kilometer ?? '') }}" placeholder="e.g. 85000">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="next_service_interval">Next Service Interval</label>
                <select id="next_service_interval" class="form-control">
                    <option value="3000">After 3,000 km</option>
                    <option value="5000" {{ $m ? '' : 'selected' }}>After 5,000 km</option>
                    <option value="10000">After 10,000 km</option>
                    <option value="custom" {{ $m ? 'selected' : '' }}>Custom Value</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="next_service_kilometer">Next Service At (km) <span class="text-danger km-required">*</span><small class="text-muted km-optional"> (optional)</small></label>
                <input type="number" min="0" name="next_service_kilometer" id="next_service_kilometer" class="form-control" value="{{ old('next_service_kilometer', $m->next_service_kilometer ?? '') }}" placeholder="Calculated automatically">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="next_service_months">Next Service By Date</label>
                <select id="next_service_months" class="form-control">
                    <option value="">No date</option>
                    <option value="3">After 3 months</option>
                    <option value="6">After 6 months</option>
                    <option value="12">After 12 months</option>
                    <option value="custom">Pick a date</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="next_service_date">Next Service Date</label>
                <input type="date" name="next_service_date" id="next_service_date" class="form-control" value="{{ old('next_service_date', $m && $m->next_service_date ? $m->next_service_date->format('Y-m-d') : '') }}">
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
            <div class="calculation-panel">
                <strong><i class="fa fas fa-info-circle"></i> Automatic Calculator:</strong>
                <span id="calc-summary">Please input current mileage to calculate next service.</span>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="details">Work done / parts changed <small class="text-muted">(one item per line — each line prints on the receipt)</small></label>
    <textarea name="details" id="details" class="form-control" rows="5" placeholder="Engine oil 5W-30 (4L)&#10;Oil filter&#10;Air filter cleaned">{{ old('details', $m->details ?? '') }}</textarea>
</div>

<style>
    .service-type-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
    .service-type-card { position: relative; display: flex; flex-direction: column; gap: 4px; border: 2px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; cursor: pointer; font-weight: normal; transition: all .15s ease; background: #fff; }
    .service-type-card input { position: absolute; opacity: 0; pointer-events: none; }
    .service-type-card i { font-size: 20px; color: #94a3b8; }
    .service-type-card strong { font-size: 15px; color: #0f172a; }
    .service-type-card small { color: #64748b; text-transform: none; letter-spacing: 0; font-weight: 400; font-size: 12.5px; }
    .service-type-card.active { border-color: #ea580c; background: #fff7ed; box-shadow: 0 4px 14px rgba(234, 88, 12, .12); }
    .service-type-card.active i { color: #ea580c; }
</style>
