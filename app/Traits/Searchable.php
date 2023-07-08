<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

class Searchable
{
    /**
     * Search scope
     */
    public function scopeSearch(Builder $query, ?string $search, array $columns): void
    {
        if ($search) {
            $query->where(function ($query) use ($columns, $search) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }
    }
}
