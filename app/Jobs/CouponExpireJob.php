<?php

namespace App\Jobs;

use App\Services\Mall\CouponService;
use App\Services\Mall\UserCouponService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponExpireJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $couponId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($couponId, $expirationTime)
    {
        $this->couponId = $couponId;
        $expirationTime = Carbon::parse($expirationTime);
        $delayInSeconds = $expirationTime->diffInSeconds(Carbon::now());
        $this->delay($delayInSeconds);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                CouponService::getInstance()->expireCoupon($this->couponId);
                UserCouponService::getInstance()->expireCoupon($this->couponId);
            });
        } catch (\Throwable $e) {
            Log::error("expire coupon job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
