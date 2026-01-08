<?php

namespace PranitDalavi\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;

class DiscountRevoked
{
    use Dispatchable;
    public function __construct(public int $userId, public int $discountId) {}
}