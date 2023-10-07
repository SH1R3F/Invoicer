<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productable extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'price', 'quantity', 'taxes', 'productable_type', 'productable_id'];

    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
        'taxes' => 'array'
    ];

    /**
     * Get & Set the displayed product price
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => $value / 100,
            set: fn (int|float $value) => $value * 100,
        );
    }

    /**
     * Relationship to products
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship to productables (quotes & invoices)
     */
    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
