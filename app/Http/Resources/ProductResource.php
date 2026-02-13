<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Product;

class ProductResource extends JsonResource
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
            'image' => $this->image ? url('/uploads/img/' . $this->image) : null,
            'description' => $this->description,
            'price' => number_format($this->variations->first()?->sell_price_inc_tax ?? $this->price, 2, '.', ''),
            'discount' => $this->discount,
            'pin' => $this->pin ?? "1",
            'rating' => new ProductRatingResource($this->productRating),
            // 'related_products' => Product::where('product_category_id', $this->product_category_id)->get(['id', 'name', 'price', 'discount', 'pin']),
        ];
    }
}
