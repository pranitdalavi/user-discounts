<?php

namespace PranitDalavi\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDiscount extends Model
{
    protected $fillable = ['user_id', 'discount_id', 'used_count'];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function canUse(): bool
    {
        return !$this->discount->usage_limit || $this->used_count < $this->discount->usage_limit;
    }
}