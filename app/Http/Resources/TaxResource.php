<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxResource extends JsonResource
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
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
            'tax' => $this->type == 'percentage' ? "{$this->value}%" : number_format($this->value, 2) . '$',
            'default' => $this->default,

            'editable'  => $request->user()?->can('update', $this->resource),
            'deletable' => $request->user()?->can('delete', $this->resource),
        ];
    }
}
