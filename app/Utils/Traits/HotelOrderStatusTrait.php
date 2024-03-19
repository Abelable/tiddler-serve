<?php

namespace App\Utils\Traits;

use App\Utils\Enums\HotelOrderEnums;
use Illuminate\Support\Str;

/**
 * @package App\Models\Order
 * @method bool canCancelHandle()
 * @method bool canDeleteHandle()
 * @method bool canPayHandle()
 * @method bool canShipHandle()
 * @method bool canCommentHandle()
 * @method bool canConfirmHandle()
 * @method bool canRefundHandle()
 * @method bool canRebuyHandle()
 * @method bool canAftersaleHandle()
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
            $status = (new \ReflectionClass(HotelOrderEnums::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [HotelOrderEnums::STATUS_CREATE],
        'pay' => [HotelOrderEnums::STATUS_CREATE],
        'confirm' => [HotelOrderEnums::STATUS_SETTLE_IN],
        'refund' => [HotelOrderEnums::STATUS_PAY],
        // 同意退款
        'agreerefund' => [
            HotelOrderEnums::STATUS_REFUND,
            HotelOrderEnums::STATUS_SUPPLIER_REFUND
        ],
        'comment' => [
            HotelOrderEnums::STATUS_CONFIRM,
            HotelOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 售后
        'aftersale' => [
            HotelOrderEnums::STATUS_CONFIRM,
            HotelOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 回购
        'rebuy' => [
            HotelOrderEnums::STATUS_CONFIRM,
            HotelOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'delete' => [
            HotelOrderEnums::STATUS_CANCEL,
            HotelOrderEnums::STATUS_AUTO_CANCEL,
            HotelOrderEnums::STATUS_ADMIN_CANCEL,
            HotelOrderEnums::STATUS_REFUND_CONFIRM,
            HotelOrderEnums::STATUS_CONFIRM,
            HotelOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'finish' => [
            HotelOrderEnums::STATUS_CONFIRM,
            HotelOrderEnums::STATUS_AUTO_CONFIRM,
        ]
    ];

    public function isHadPaid()
    {
        return !in_array($this->status, [
            HotelOrderEnums::STATUS_CREATE,
            HotelOrderEnums::STATUS_CANCEL,
            HotelOrderEnums::STATUS_AUTO_CANCEL,
            HotelOrderEnums::STATUS_ADMIN_CANCEL,
        ]);
    }

    public function getCanHandleOptions()
    {
        return [
            'cancel' => $this->canCancelHandle(),
            'delete' => $this->canDeleteHandle(),
            'pay' => $this->canPayHandle(),
            'comment' => $this->canCommentHandle(),
            'confirm' => $this->canConfirmHandle(),
            'refund' => $this->canRefundHandle(),
            'aftersale' => $this->canAftersaleHandle(),
            'rebuy' => $this->canRebuyHandle(),
            'finish' => $this->canFinishHandle(),
        ];
    }
}
