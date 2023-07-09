<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Product;
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

        foreach ($sort as ['key' => $key, 'order' => $order]) {
            switch ($key) {
                case 'user':
                    $this->applyUserOrder($order, $query);
                    break;
                case 'role':
                    $this->applyRoleOrder($order, $query);
                    break;
                case 'category':
                    $this->applyCategoryOrder($order, $query);
                    break;
                default:
                    if (in_array($key, \Illuminate\Support\Facades\Schema::getColumnListing($this->getTable()))) {
                        $query->orderBy($key, $order);
                    }
            }
        }
    }

    private function applyUserOrder(string $order, Builder $query)
    {
        if ($this instanceof User) {
            $query->orderBy('name', $order);
        }
    }

    private function applyRoleOrder(string $order, Builder $query)
    {
        if ($this instanceof User) {
            $query->orderBy(function ($query) {
                return $query->from('model_has_roles')
                    ->whereRaw("`model_has_roles`.model_id = `users`.id")
                    ->select('role_id');
            }, $order);
        }
    }

    private function applyCategoryOrder(string $order, Builder $query)
    {
        if ($this instanceof Product) {
            $query->orderBy(function ($query) {
                return $query->from('categories')
                    ->whereRaw("`categories`.id = `products`.category_id")
                    ->select('name');
            }, $order);
        }
    }
}
