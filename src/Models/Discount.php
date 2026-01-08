<?php

namespace PranitDalavi\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = ['name', 'type', 'value', 'usage_limit', 'active', 'expires_at'];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function userDiscounts(): HasMany
    {
        return $this->hasMany(UserDiscount::class);
    }

    public function isActive(): bool
    {
        return $this->active && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
