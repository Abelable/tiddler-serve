<?php

namespace App\Jobs;

use App\Services\HotelOrderService;
use App\Services\MealTicketOrderService;
use App\Services\OrderService;
use App\Services\ScenicOrderService;
use App\Services\SetMealOrderService;
use App\Utils\Enums\ProductType;
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

    private $productType;
    private $userId;
    private $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productType, $userId, $orderId)
    {
        $this->productType = $productType;
        $this->userId = $userId;
        $this->orderId = $orderId;
        $this->delay(now()->addHours(24));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            switch ($this->productType) {
                case ProductType::SCENIC:
                    ScenicOrderService::getInstance()->systemCancel($this->userId, $this->orderId);
                    break;

                case ProductType::HOTEL:
                    HotelOrderService::getInstance()->systemCancel($this->userId, $this->orderId);
                    break;

                case ProductType::GOODS:
                    OrderService::getInstance()->systemAutoCancel($this->userId, $this->orderId);
                    break;

                case ProductType::MEAL_TICKET:
                    MealTicketOrderService::getInstance()->systemCancel($this->userId, $this->orderId);
                    break;

                case ProductType::SET_MEAL:
                    SetMealOrderService::getInstance()->systemCancel($this->userId, $this->orderId);
                    break;
            }
        } catch (\Throwable $e) {
            Log::error("cancel overTime order job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
