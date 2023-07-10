<?php

namespace App\Traits;

use App\Models\Quote;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Search scope
     */
    public function scopeSearch(Builder $query, ?string $search, array $columns): void
    {
        if ($search) {
            $query->where(function ($query) use ($columns, $search) {
                foreach ($columns as $column) {
                    if (Str::contains($column, '.')) {
                        $this->searchRelationship($query, $column, $search);
                    } else {
                        $query->orWhere($column, 'LIKE', "%{$search}%");
                    }
                }
            });
        }
    }

    private function searchRelationship(Builder &$query, string $column, string $search): void
    {
        [$relation, $attribute] = explode('.', $column);
        $query->whereHas($relation, fn ($query) => $query->where($attribute, 'LIKE', "%{$search}%"));
    }
}
