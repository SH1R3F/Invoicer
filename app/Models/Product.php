<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Searchable, Orderable;

    protected $fillable = ['sku', 'name', 'description', 'image', 'category_id', 'price'];
}
