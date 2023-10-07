<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Enums\QuoteStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * Get the total quote amount
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->quotables->reduce(function (int $carry, Productable $quotable) {
                    $line_price = $quotable->getAttributes()['price'] * $quotable->quantity;
                    collect($quotable->taxes ?? [])->map(function ($tax) use (&$carry, $line_price) {
                        $carry += $tax['type'] == 'fixed' ? ($tax['value'] * 100) : ($tax['value'] / 100) * $line_price;
                    });
                    return $carry + $line_price;
                }, 0) / 100;
            },
        );
    }

    /**
     * Relationship to client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to quote items
     */
    public function quotables(): MorphMany
    {
        return $this->morphMany(Productable::class, 'productable');
    }
}
