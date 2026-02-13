<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Get pricing information
        $price = null;
        $discount = $this->discount ?? null;
        
        // Get the minimum price from vehicle prices, or null if no prices exist
        if ($this->serviceVehiclePrices && $this->serviceVehiclePrices->isNotEmpty()) {
            $minPrice = $this->serviceVehiclePrices->min('price');
            $price = $minPrice ? number_format($minPrice, 2, '.', '') : null;
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $price,
            'discount' => $discount,
        ];
    }
}
