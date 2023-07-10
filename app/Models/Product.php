<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Traits\Searchable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Product extends Model
{
    use HasFactory, Searchable, Orderable;

    protected $fillable = ['sku', 'name', 'description', 'image', 'category_id', 'price'];


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
     * Filter users in admin panel
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
    }

    /**
     * Relationship to category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to quotes
     */
    public function quotes(): MorphToMany
    {
        return $this->morphedByMany(Quote::class, 'productable');
    }

    /**
     * Unique sku generator method
     */
    public static function uniqueSku(): string
    {
        $sku = Str::random(8);

        while (static::where('sku', $sku)->exists()) {
            $sku = Str::random(8);
        }

        return $sku;
    }
}
