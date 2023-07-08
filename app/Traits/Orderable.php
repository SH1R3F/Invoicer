<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Orderable
{
    /**
     * Order scope
     */
    public function scopeOrder(Builder $query, array $sort): void
    {
        if (!count($sort)) {
            $query->orderBy('id', 'DESC');
            return;
        }

        foreach ($sort as [$key, $order]) {
            switch ($key) {
                default:
                    if (in_array($key, \Illuminate\Support\Facades\Schema::getColumnListing($this->getTable()))) {
                        $query->orderBy($key, $order);
                    }
            }
        }
    }
}
