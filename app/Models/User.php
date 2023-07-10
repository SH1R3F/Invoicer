<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Orderable;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, Searchable, Orderable;

    protected $guard_name = 'sanctum';


    public const DEFAULT_PICTURE = 'assets/img/user.png';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Filter users in admin panel
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['role'])) {
            $query->whereHas('roles', fn ($query) => $query->where('name', $filters['role']));
        }
    }

    /**
     * Relationship to quotes
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
