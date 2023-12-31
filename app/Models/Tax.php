<?php

namespace App\Models;

use App\Traits\Orderable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory, Searchable, Orderable;

    protected $fillable = ['name', 'value', 'type', 'default'];

    protected $casts = [
        'value'   => 'integer',
        'default' => 'boolean'
    ];
}
