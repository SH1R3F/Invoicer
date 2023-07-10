<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'taxes' => $this->taxes,
            'productable_id' => $this->productable_id,
            'productable_type' => $this->productable_type,
            'productable' => $this->whenLoaded('productable'),
        ];
    }
}
