<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('client')),
            'quote_number' => $this->quote_number,
            'quote_date' => $this->quote_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'status' => $this->status,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'discount' => $this->discount_type == 'percentage' ? "{$this->discount_value}%" : number_format($this->discount_value, 2) . '$',
            'notes' => $this->notes,
            'quotables' => ProductableResource::collection($this->whenLoaded('quotables')),
            'amount' => number_format($this->amount, 2),

            'editable'  => $request->user()?->can('update', $this->resource),
            'deletable' => $request->user()?->can('delete', $this->resource),
        ];
    }
}
