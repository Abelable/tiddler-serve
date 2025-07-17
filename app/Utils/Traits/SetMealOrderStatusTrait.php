<?php

namespace App\Utils\Traits;

use App\Utils\Enums\SetMealOrderStatus;
use Illuminate\Support\Str;

/**
 * @package App\Models\Order
 * @method bool canCancelHandle()
 * @method bool canDeleteHandle()
 * @method bool canPayHandle()
 * @method bool canCommentHandle()
 * @method bool canApproveHandle()
 * @method bool canConfirmHandle()
 * @method bool canRefundHandle()
 * @method bool canReBuyHandle()
 * @method bool canAfterSaleHandle()
 * @method bool canAgreeRefundHandle()
 * @method bool canFinishHandle()
 * @method bool isPayStatus()
 * @method bool isConfirmStatus()
 * @method bool isCancelStatus()
 * @method bool isAutoCancelStatus()
 * @method bool isRefundStatus()
 * @method bool isRefundConfirmStatus()
 * @method bool isAutoConfirmStatus()
 */
trait SetMealOrderStatusTrait
{
    public function __call($name, $arguments)
    {
        if (Str::is('can*Handle', $name)) {
            if (is_null($this->status)) {
                throw new \Exception("order status is null when call method[$name]!");
            }
            $key = Str::of($name)
                ->replaceFirst('can', '')
                ->replaceLast('Handle', '')
                ->lower();
            return in_array($this->status, $this->canHandleMap[(string)$key]);
        } elseif (Str::is('is*Status', $name)) {
            if (is_null($this->status)) {
                throw new \Exception("order status is null when call method[$name]!");
            }
            $key = Str::of($name)
                ->replaceFirst('is', '')
                ->replaceLast('Status', '')
                ->snake()->upper()->prepend('STATUS');
            $status = (new \ReflectionClass(SetMealOrderStatus::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [SetMealOrderStatus::CREATED],
        'pay' => [SetMealOrderStatus::CREATED],
        'delete' => [
            SetMealOrderStatus::CANCELED,
            SetMealOrderStatus::AUTO_CANCELED,
            SetMealOrderStatus::ADMIN_CANCELED,
        ],
        'refund' => [SetMealOrderStatus::PAID],
        'agreeRefund' => [
            SetMealOrderStatus::REFUNDING
        ],
        'approve' => [SetMealOrderStatus::PAID],
        'confirm' => [SetMealOrderStatus::MERCHANT_APPROVED],
        'comment' => [
            SetMealOrderStatus::CONFIRMED,
            SetMealOrderStatus::AUTO_CONFIRMED,
            SetMealOrderStatus::ADMIN_CONFIRMED
        ],
        'finish' => [
            SetMealOrderStatus::CONFIRMED,
            SetMealOrderStatus::AUTO_CONFIRMED,
            SetMealOrderStatus::ADMIN_CONFIRMED
        ],
        'afterSale' => [
            SetMealOrderStatus::CONFIRMED,
            SetMealOrderStatus::AUTO_CONFIRMED,
            SetMealOrderStatus::ADMIN_CONFIRMED,
            SetMealOrderStatus::FINISHED,
            SetMealOrderStatus::AUTO_FINISHED,
        ],
        'reBuy' => [
            SetMealOrderStatus::CONFIRMED,
            SetMealOrderStatus::AUTO_CONFIRMED,
            SetMealOrderStatus::ADMIN_CONFIRMED,
            SetMealOrderStatus::FINISHED,
            SetMealOrderStatus::AUTO_FINISHED,
        ],
    ];

    public function isPaid()
    {
        return !in_array($this->status, [
            SetMealOrderStatus::CREATED,
            SetMealOrderStatus::CANCELED,
            SetMealOrderStatus::AUTO_CANCELED,
            SetMealOrderStatus::ADMIN_CANCELED,
        ]);
    }

    public function getCanHandleOptions()
    {
        return [
            'cancel' => $this->canCancelHandle(),
            'pay' => $this->canPayHandle(),
            'delete' => $this->canDeleteHandle(),
            'refund' => $this->canRefundHandle(),
            'agreeRefund' => $this->canAgreeRefundHandle(),
            'approve' => $this->canApproveHandle(),
            'confirm' => $this->canConfirmHandle(),
            'comment' => $this->canCommentHandle(),
            'finish' => $this->canFinishHandle(),
            'afterSale' => $this->canAftersaleHandle(),
            'reBuy' => $this->canRebuyHandle(),
        ];
    }
}
