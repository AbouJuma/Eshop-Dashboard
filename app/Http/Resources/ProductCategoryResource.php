<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\ProductCategory;

class ProductCategoryResource extends JsonResource
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
                'name' => $this->name,
                // 'products' => ProductResource::collection($this->products->where('pin',1)->take(20)),
                'products' => ProductResource::collection($this->products),
            ];
    }
}
