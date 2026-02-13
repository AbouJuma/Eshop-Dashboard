<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceVehiclePriceResource extends JsonResource
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
            'id' => $this->sub_service_id,
            'title' => $this->subService->title,
            'description'=> $this->subService->description,
            'price' =>(string) $this->price,
            'discount' => $this->discount,
            'image' => $this->subService->image,
            'service_id' => $this->subService->service_id,
        ];
    }
}
