<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
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
            // Remove 'services/' prefix if it exists to avoid duplication
            $imagePath = $this->image;
            if (strpos($imagePath, 'services/') === 0) {
                $imagePath = substr($imagePath, 9); // Remove 'services/' prefix
            }
            $imageUrl = url('/uploads/services/' . $imagePath);
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $imageUrl,
            'description' => $this->description,
        ];
    }
}
