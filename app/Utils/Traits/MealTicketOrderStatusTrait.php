<?php

namespace App\Utils\Traits;

use App\Utils\Enums\MealTicketOrderStatus;
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
trait MealTicketOrderStatusTrait
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
            $status = (new \ReflectionClass(MealTicketOrderStatus::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [MealTicketOrderStatus::CREATED],
        'pay' => [MealTicketOrderStatus::CREATED],
        'delete' => [
            MealTicketOrderStatus::CANCELED,
            MealTicketOrderStatus::AUTO_CANCELED,
            MealTicketOrderStatus::ADMIN_CANCELED,
        ],
        'refund' => [MealTicketOrderStatus::PAID],
        'agreeRefund' => [
            MealTicketOrderStatus::REFUNDING
        ],
        'approve' => [MealTicketOrderStatus::PAID],
        'confirm' => [MealTicketOrderStatus::MERCHANT_APPROVED],
        'comment' => [
            MealTicketOrderStatus::CONFIRMED,
            MealTicketOrderStatus::AUTO_CONFIRMED,
            MealTicketOrderStatus::ADMIN_CONFIRMED
        ],
        'finish' => [
            MealTicketOrderStatus::CONFIRMED,
            MealTicketOrderStatus::AUTO_CONFIRMED,
            MealTicketOrderStatus::ADMIN_CONFIRMED
        ],
        'afterSale' => [
            MealTicketOrderStatus::CONFIRMED,
            MealTicketOrderStatus::AUTO_CONFIRMED,
            MealTicketOrderStatus::ADMIN_CONFIRMED,
            MealTicketOrderStatus::FINISHED,
            MealTicketOrderStatus::AUTO_FINISHED,
        ],
        'reBuy' => [
            MealTicketOrderStatus::CONFIRMED,
            MealTicketOrderStatus::AUTO_CONFIRMED,
            MealTicketOrderStatus::ADMIN_CONFIRMED,
            MealTicketOrderStatus::FINISHED,
            MealTicketOrderStatus::AUTO_FINISHED,
        ],
    ];

    public function isPaid()
    {
        return !in_array($this->status, [
            MealTicketOrderStatus::CREATED,
            MealTicketOrderStatus::CANCELED,
            MealTicketOrderStatus::AUTO_CANCELED,
            MealTicketOrderStatus::ADMIN_CANCELED,
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
