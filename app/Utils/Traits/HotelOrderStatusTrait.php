<?php

namespace App\Utils\Traits;

use App\Utils\Enums\HotelOrderStatus;
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
trait HotelOrderStatusTrait
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
            $status = (new \ReflectionClass(HotelOrderStatus::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [HotelOrderStatus::CREATED],
        'pay' => [HotelOrderStatus::CREATED],
        'delete' => [
            HotelOrderStatus::CANCELED,
            HotelOrderStatus::AUTO_CANCELED,
            HotelOrderStatus::ADMIN_CANCELED,
        ],
        'refund' => [HotelOrderStatus::PAID],
        'agreeRefund' => [
            HotelOrderStatus::REFUNDING
        ],
        'approve' => [HotelOrderStatus::PAID],
        'confirm' => [HotelOrderStatus::MERCHANT_APPROVED],
        'comment' => [
            HotelOrderStatus::CONFIRMED,
            HotelOrderStatus::AUTO_CONFIRMED,
            HotelOrderStatus::ADMIN_CONFIRMED
        ],
        'finish' => [
            HotelOrderStatus::CONFIRMED,
            HotelOrderStatus::AUTO_CONFIRMED,
            HotelOrderStatus::ADMIN_CONFIRMED
        ],
        'afterSale' => [
            HotelOrderStatus::CONFIRMED,
            HotelOrderStatus::AUTO_CONFIRMED,
            HotelOrderStatus::ADMIN_CONFIRMED,
            HotelOrderStatus::FINISHED,
            HotelOrderStatus::AUTO_FINISHED,
        ],
        'reBuy' => [
            HotelOrderStatus::CONFIRMED,
            HotelOrderStatus::AUTO_CONFIRMED,
            HotelOrderStatus::ADMIN_CONFIRMED,
            HotelOrderStatus::FINISHED,
            HotelOrderStatus::AUTO_FINISHED,
        ],
    ];

    public function isPaid()
    {
        return !in_array($this->status, [
            HotelOrderStatus::CREATED,
            HotelOrderStatus::CANCELED,
            HotelOrderStatus::AUTO_CANCELED,
            HotelOrderStatus::ADMIN_CANCELED,
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
