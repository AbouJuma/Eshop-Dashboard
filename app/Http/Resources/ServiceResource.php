<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image ? url('/uploads/' . $this->image) : null,
            'description' => $this->description,
            'sub_services' => SubServiceResource::collection(
                $this->subServices->load('serviceVehiclePrices')
            ),
        ];
    }
}
