<?php

namespace PranitDalavi\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DiscountApplied
{
    use Dispatchable;
    public function __construct(public int $userId, public int $discountId, public float $amount) {}
}
