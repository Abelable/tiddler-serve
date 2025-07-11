<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\ScenicShopIncomeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScenicShopIncomeConfirmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $incomeId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($incomeId)
    {
        $this->incomeId = $incomeId;
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
            ScenicShopIncomeService::getInstance()->updateIncomeToConfirmStatus($this->incomeId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
