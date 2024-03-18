<?php

namespace App\Utils\Traits;

use App\Utils\Enums\ScenicOrderEnums;
use Illuminate\Support\Str;

/**
 * @package App\Models\Order
 * @method bool canCancelHandle()
 * @method bool canDeleteHandle()
 * @method bool canPayHandle()
 * @method bool canCommentHandle()
 * @method bool canConfirmHandle()
 * @method bool canRefundHandle()
 * @method bool canRebuyHandle()
 * @method bool canAftersaleHandle()
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
            $status = (new \ReflectionClass(ScenicOrderEnums::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [ScenicOrderEnums::STATUS_CREATE],
        'pay' => [ScenicOrderEnums::STATUS_CREATE],
        'confirm' => [ScenicOrderEnums::STATUS_PAY],
        'refund' => [ScenicOrderEnums::STATUS_PAY],
        // 同意退款
        'agreerefund' => [ScenicOrderEnums::STATUS_REFUND],
        'comment' => [
            ScenicOrderEnums::STATUS_CONFIRM,
            ScenicOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 售后
        'aftersale' => [
            ScenicOrderEnums::STATUS_CONFIRM,
            ScenicOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 回购
        'rebuy' => [
            ScenicOrderEnums::STATUS_CONFIRM,
            ScenicOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'delete' => [
            ScenicOrderEnums::STATUS_CANCEL,
            ScenicOrderEnums::STATUS_AUTO_CANCEL,
            ScenicOrderEnums::STATUS_ADMIN_CANCEL,
            ScenicOrderEnums::STATUS_REFUND_CONFIRM,
            ScenicOrderEnums::STATUS_CONFIRM,
            ScenicOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'finish' => [
            ScenicOrderEnums::STATUS_CONFIRM,
            ScenicOrderEnums::STATUS_AUTO_CONFIRM,
        ]
    ];

    public function isHadPaid()
    {
        return !in_array($this->status, [
            ScenicOrderEnums::STATUS_CREATE,
            ScenicOrderEnums::STATUS_CANCEL,
            ScenicOrderEnums::STATUS_AUTO_CANCEL,
            ScenicOrderEnums::STATUS_ADMIN_CANCEL,
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
