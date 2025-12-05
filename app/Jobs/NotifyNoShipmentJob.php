<?php

namespace App\Jobs;

use App\Utils\WxMpServe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyNoShipmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $openid;
    private $payId;
    private $productName;
    private $logisticsType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($openid, $payId, $productName, $logisticsType = 4)
    {
        $this->openid = $openid;
        $this->payId = $payId;
        $this->productName = $productName;
        $this->logisticsType = $logisticsType;

        $this->delay(now()->addSeconds(10));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            WxMpServe::new()->notifyNoShipment($this->openid, $this->payId, $this->productName, $this->logisticsType);
        } catch (\Throwable $e) {
            Log::error("notify no shipment job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
