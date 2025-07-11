<?php

namespace App\Utils\Traits;

use App\Utils\Enums\ScenicOrderStatus;
use Illuminate\Support\Str;

/**
 * @package App\Models\Order
 * @method bool canCancelHandle()
 * @method bool canDeleteHandle()
 * @method bool canPayHandle()
 * @method bool canCommentHandle()
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
trait ScenicOrderStatusTrait
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
            $status = (new \ReflectionClass(ScenicOrderStatus::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [ScenicOrderStatus::CREATED],
        'pay' => [ScenicOrderStatus::CREATED],
        'delete' => [
            ScenicOrderStatus::CANCELED,
            ScenicOrderStatus::AUTO_CANCELED,
            ScenicOrderStatus::ADMIN_CANCELED,
        ],
        'refund' => [ScenicOrderStatus::PAID],
        'agreeRefund' => [
            ScenicOrderStatus::REFUNDING,
            ScenicOrderStatus::MERCHANT_REFUNDING
        ],
        'confirm' => [ScenicOrderStatus::MERCHANT_APPROVED],
        'comment' => [
            ScenicOrderStatus::CONFIRMED,
            ScenicOrderStatus::AUTO_CONFIRMED,
            ScenicOrderStatus::ADMIN_CONFIRMED
        ],
        'finish' => [
            ScenicOrderStatus::CONFIRMED,
            ScenicOrderStatus::AUTO_CONFIRMED,
            ScenicOrderStatus::ADMIN_CONFIRMED
        ],
        'afterSale' => [
            ScenicOrderStatus::CONFIRMED,
            ScenicOrderStatus::AUTO_CONFIRMED,
            ScenicOrderStatus::ADMIN_CONFIRMED,
            ScenicOrderStatus::FINISHED,
            ScenicOrderStatus::AUTO_FINISHED,
        ],
        'reBuy' => [
            ScenicOrderStatus::CONFIRMED,
            ScenicOrderStatus::AUTO_CONFIRMED,
            ScenicOrderStatus::ADMIN_CONFIRMED,
            ScenicOrderStatus::FINISHED,
            ScenicOrderStatus::AUTO_FINISHED,
        ],
    ];

    public function isPaid()
    {
        return !in_array($this->status, [
            ScenicOrderStatus::CREATED,
            ScenicOrderStatus::CANCELED,
            ScenicOrderStatus::AUTO_CANCELED,
            ScenicOrderStatus::ADMIN_CANCELED,
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
            'confirm' => $this->canConfirmHandle(),
            'comment' => $this->canCommentHandle(),
            'finish' => $this->canFinishHandle(),
            'afterSale' => $this->canAftersaleHandle(),
            'reBuy' => $this->canRebuyHandle(),
        ];
    }
}
