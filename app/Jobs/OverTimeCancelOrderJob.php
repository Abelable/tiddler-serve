<?php

namespace App\Jobs;

use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OverTimeCancelOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $orderId)
    {
        $this->userId = $userId;
        $this->orderId = $orderId;
        $this->delay(now()->addMinute(30));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            OrderService::getInstance()->systemAutoCancel($this->userId, $this->orderId);
        } catch (\Throwable $e) {
            Log::error("cancel overTime order job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
