<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'description',
        'status',
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public function subServices()
    {
        return $this->hasMany(SubService::class, 'service_id', 'id')->where('status', 1);
    }

    //delete image
    public function deleteImage()
    {
        if ($this->image) {
            try {
                unlink(storage_path('app/public/' . str_replace("storage/","",$this->image)));
            } catch (\Throwable $th) {
            }
            
        }
    }

    //delete sub services
    public function deleteSubServices()
    {
        try {
            // Get all sub-services for this service and delete them
            $subServices = \App\Models\SubService::where('service_id', $this->id)->get();
            
            foreach ($subServices as $subService) {
                $subService->delete();
            }
        } catch (\Throwable $th) {
            // Log error if needed
            \Log::error('Error deleting sub-services: ' . $th->getMessage());
        }
    }

    //delete sub services in boot method
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($service) {
            $service->deleteImage();
            $service->deleteSubServices();
        });
    }
}
