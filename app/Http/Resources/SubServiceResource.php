<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Handle image URL generation
        $imageUrl = null;
        if ($this->image) {
            if (strpos($this->image, 'storage/') === 0) {
                // Storage path - use asset() for storage files
                $imageUrl = asset($this->image);
            } else {
                // Regular uploads path
                $imageUrl = url('/uploads/' . $this->image);
            }
        }
        
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
            'description'=> $this->description,
            'image' => $imageUrl,
            'price' => $price,
            'discount' => $discount,
            'service_id' => $this->service_id,
        ];
    }
}
