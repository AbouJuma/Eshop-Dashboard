{{-- Print dropdown for one maintenance record: next-service card or receipt, in any paper size. --}}
@php($defaultDoc = $record->defaultDocument())
<div class="btn-group print-menu" style="display:inline-block;">
    <button type="button" class="action-btn action-btn-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Print card / receipt">
        <i class="fa fas fa-print"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">
        <li class="dropdown-header"><i class="fa fas fa-id-card"></i> Next service card{{ $defaultDoc === 'card' ? ' ★' : '' }}</li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'card', 'size' => 'label']) }}">Label sticker 80 × 50 mm</a></li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'card', 'size' => '80mm']) }}">Thermal 80 mm</a></li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'card', 'size' => 'a5']) }}">A5 / A4 page</a></li>
        <li role="separator" class="divider"></li>
        <li class="dropdown-header"><i class="fa fas fa-receipt"></i> Company receipt{{ $defaultDoc === 'receipt' ? ' ★' : '' }}</li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'receipt', 'size' => '80mm']) }}">Thermal 80 mm</a></li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'receipt', 'size' => '58mm']) }}">Thermal 58 mm</a></li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'receipt', 'size' => 'a5']) }}">A5</a></li>
        <li><a target="_blank" href="{{ route('car-maintenance.print', ['id' => $record->id, 'doc' => 'receipt', 'size' => 'a4']) }}">A4</a></li>
    </ul>
</div>
