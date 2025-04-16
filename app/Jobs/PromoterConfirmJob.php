<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\PromoterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PromoterConfirmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $orderGoodsId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($orderGoodsId)
    {
        $this->orderGoodsId = $orderGoodsId;
        $this->delay(now()->addDays(7));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            PromoterService::getInstance()->createPromoterByGift($this->orderGoodsId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
