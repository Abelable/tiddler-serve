<?php

namespace App\Utils\Traits;

use App\Utils\Enums\OrderStatus;
use Illuminate\Support\Str;

/**
 * @package App\Models\Order
 * @method bool canCancelHandle()
 * @method bool canDeleteHandle()
 * @method bool canPayHandle()
 * @method bool canExportHandle()
 * @method bool canShipHandle()
 * @method bool canCommentHandle()
 * @method bool canConfirmHandle()
 * @method bool canRefundHandle()
 * @method bool canReBuyHandle()
 * @method bool canAfterSaleHandle()
 * @method bool canAgreeRefundHandle()
 * @method bool canFinishHandle()
 * @method bool isPayStatus()
 * @method bool isShipStatus()
 * @method bool isConfirmStatus()
 * @method bool isCancelStatus()
 * @method bool isAutoCancelStatus()
 * @method bool isRefundStatus()
 * @method bool isRefundConfirmStatus()
 * @method bool isAutoConfirmStatus()
 */
trait OrderStatusTrait
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
            $status = (new \ReflectionClass(OrderStatus::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [OrderStatus::CREATED],
        'pay' => [OrderStatus::CREATED],
        'delete' => [
            OrderStatus::CANCELED,
            OrderStatus::AUTO_CANCELED,
            OrderStatus::ADMIN_CANCELED,
        ],
        'refund' => [
            OrderStatus::PAID,
            OrderStatus::PENDING_VERIFICATION,
            OrderStatus::EXPORTED,
        ],
        'agreeRefund' => [OrderStatus::REFUNDING],
        'export' => [OrderStatus::PAID],
        'ship' => [OrderStatus::PAID],
        'confirm' => [OrderStatus::SHIPPED, OrderStatus::PENDING_VERIFICATION],
        'comment' => [
            OrderStatus::CONFIRMED,
            OrderStatus::AUTO_CONFIRMED,
            OrderStatus::ADMIN_CONFIRMED,
        ],
        'finish' => [
            OrderStatus::CONFIRMED,
            OrderStatus::AUTO_CONFIRMED,
            OrderStatus::ADMIN_CONFIRMED,
        ],
        'afterSale' => [
            OrderStatus::CONFIRMED,
            OrderStatus::AUTO_CONFIRMED,
            OrderStatus::ADMIN_CONFIRMED,
            OrderStatus::FINISHED,
            OrderStatus::AUTO_FINISHED,
        ],
        'reBuy' => [
            OrderStatus::CONFIRMED,
            OrderStatus::AUTO_CONFIRMED,
            OrderStatus::ADMIN_CONFIRMED,
            OrderStatus::FINISHED,
            OrderStatus::AUTO_FINISHED,
        ],
    ];

    public function isPaid()
    {
        return !in_array($this->status, [
            OrderStatus::CREATED,
            OrderStatus::CANCELED,
            OrderStatus::AUTO_CANCELED,
            OrderStatus::AUTO_CANCELED,
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
            'export' => $this->canExportHandle(),
            'ship' => $this->canShipHandle(),
            'confirm' => $this->canConfirmHandle(),
            'comment' => $this->canCommentHandle(),
            'finish' => $this->canFinishHandle(),
            'afterSale' => $this->canAftersaleHandle(),
            'reBuy' => $this->canRebuyHandle(),
        ];
    }
}
