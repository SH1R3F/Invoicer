<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image ? Storage::url($this->image) : null,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category'),
            'price' => $this->price,
            'editable'  => $request->user()?->can('update', $this->resource),
            'deletable' => $request->user()?->can('delete', $this->resource),
        ];
    }
}
