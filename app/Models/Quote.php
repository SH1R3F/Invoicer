<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Enums\QuoteStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Quote extends Model
{
    use HasFactory, Searchable, Orderable;

    protected $fillable = ['user_id', 'quote_number', 'quote_date', 'due_date', 'status', 'discount_type', 'discount_value', 'notes'];

    protected $casts = [
        'quote_date'     => 'date',
        'due_date'       => 'date',
        'status'         => QuoteStatus::class,
        'discount_value' => 'integer'
    ];

    /**
     * Relationship to client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to products
     */
    public function products(): MorphToMany
    {
        return $this->morphToMany(Product::class, 'productable')->withPivot(['product_id', 'name', 'price', 'quantity', 'taxes']);
    }
}
