<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, Searchable, Orderable;

    protected $fillable = ['name'];

    /**
     * Relationship to products
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
