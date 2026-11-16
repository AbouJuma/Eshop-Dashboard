<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Business;

class CarMaintenance extends Model
{
    protected $table = 'car_maintenances';

    /** Service categories. Oil changes get a next-service card; the others a company receipt. */
    const TYPES = [
        'oil_change' => 'Oil Change',
        'periodic_maintenance' => 'Periodic Maintenance',
        'repair' => 'Repair Work',
    ];

    /** Mileage is required for oil changes only. */
    const MILEAGE_REQUIRED = ['oil_change'];

    protected $fillable = [
        'business_id',
        'car_id',
        'service_date',
        'serviced_kilometer',
        'next_service_kilometer',
        'next_service_date',
        'service_type',
        'details',
        'cost',
        'created_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'serviced_kilometer' => 'integer',
        'next_service_kilometer' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->service_type] ?? ucwords(str_replace('_', ' ', (string) $this->service_type));
    }

    public function getReceiptNumberAttribute(): string
    {
        return 'SR-'.str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function hasNextService(): bool
    {
        return $this->next_service_kilometer || $this->next_service_date;
    }

    /** Document printed by default: next-service card for oil changes, receipt otherwise. */
    public function defaultDocument(): string
    {
        return $this->service_type === 'oil_change' && $this->hasNextService() ? 'card' : 'receipt';
    }

    /** Validation for the service part of a record (portal and app). */
    public static function serviceRules(Request $request): array
    {
        $mileage = in_array($request->input('service_type'), self::MILEAGE_REQUIRED) ? 'required' : 'nullable';

        return [
            'service_type' => 'required|string|in:'.implode(',', array_keys(self::TYPES)),
            'service_date' => 'required|date',
            'details' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'serviced_kilometer' => $mileage.'|integer|min:0',
            'next_service_kilometer' => $mileage.'|integer|min:0',
            'next_service_date' => 'nullable|date|after_or_equal:service_date',
        ];
    }

    public static function serviceData(Request $request): array
    {
        return [
            'service_date' => $request->input('service_date'),
            'service_type' => $request->input('service_type'),
            'serviced_kilometer' => $request->filled('serviced_kilometer') ? (int) $request->input('serviced_kilometer') : null,
            'next_service_kilometer' => $request->filled('next_service_kilometer') ? (int) $request->input('next_service_kilometer') : null,
            'next_service_date' => $request->input('next_service_date') ?: null,
            'details' => $request->input('details'),
            'cost' => $request->input('cost') ?: 0,
        ];
    }
}
