<?php

namespace PranitDalavi\UserDiscounts\Services;

use PranitDalavi\UserDiscounts\Models\Discount;
use PranitDalavi\UserDiscounts\Models\UserDiscount;
use PranitDalavi\UserDiscounts\Models\DiscountAudit;
use PranitDalavi\UserDiscounts\Events\DiscountAssigned;
use PranitDalavi\UserDiscounts\Events\DiscountRevoked;
use PranitDalavi\UserDiscounts\Events\DiscountApplied;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DiscountService
{
    public function assign(int $userId, int $discountId): void
    {
        $userDiscount = UserDiscount::firstOrCreate([
            'user_id' => $userId,
            'discount_id' => $discountId,
        ]);

        event(new DiscountAssigned($userId, $discountId));

        DiscountAudit::create([
            'user_id' => $userId,
            'discount_id' => $discountId,
            'action' => 'assigned',
        ]);
    }

    public function revoke(int $userId, int $discountId): void
    {
        UserDiscount::where(['user_id'=>$userId,'discount_id'=>$discountId])->delete();

        event(new DiscountRevoked($userId, $discountId));

        DiscountAudit::create([
            'user_id'=>$userId,
            'discount_id'=>$discountId,
            'action'=>'revoked',
        ]);
    }

    public function eligibleFor(int $userId)
    {
        return UserDiscount::where('user_id', $userId)
            ->whereHas('discount', fn($q) => $q->where('active', true)
                ->where(fn($q2) => $q2->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            )
            ->get()
            ->filter(fn(UserDiscount $ud) => $ud->canUse());
    }

    public function apply(int $userId, float $amount): float
    {
        return DB::transaction(function() use ($userId, $amount) {
            $eligible = $this->eligibleFor($userId)
                ->sortBy(fn(UserDiscount $ud) => array_search($ud->discount->type, Config::get('discounts.stacking_order', [])));

            $originalAmount = $amount;

            foreach ($eligible as $userDiscount) {
                $discount = $userDiscount->discount;
                $applyAmount = match($discount->type) {
                    'percentage' => min($discount->value, Config::get('discounts.max_percentage', 100)) * $amount / 100,
                    'fixed' => $discount->value,
                };

                $rounding = Config::get('discounts.rounding','ceil');
                $applyAmount = match($rounding) {
                    'ceil' => ceil($applyAmount),
                    'floor' => floor($applyAmount),
                    default => round($applyAmount,2),
                };

                $amount -= $applyAmount;

                // prevent negative
                $amount = max($amount,0);

                // increment usage safely
                $userDiscount->increment('used_count');

                event(new DiscountApplied($userId, $discount->id, $applyAmount));

                DiscountAudit::create([
                    'user_id' => $userId,
                    'discount_id' => $discount->id,
                    'action' => 'applied',
                    'amount' => $applyAmount,
                ]);
            }

            return $amount;
        });
    }
}
