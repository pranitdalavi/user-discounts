<?php

namespace PranitDalavi\UserDiscounts\Traits;

use PranitDalavi\UserDiscounts\Services\DiscountService;

trait HasDiscounts
{
    public function assignDiscount(int $discountId): void
    {
        app(DiscountService::class)->assign($this->id, $discountId);
    }

    public function revokeDiscount(int $discountId): void
    {
        app(DiscountService::class)->revoke($this->id, $discountId);
    }

    public function eligibleDiscounts()
    {
        return app(DiscountService::class)->eligibleFor($this->id);
    }

    public function applyDiscounts(float $amount): float
    {
        return app(DiscountService::class)->apply($this->id, $amount);
    }
}
