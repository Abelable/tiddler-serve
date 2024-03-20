<?php

namespace App\Utils\Traits;

use App\Utils\Enums\SetMealOrderEnums;
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
            $status = (new \ReflectionClass(SetMealOrderEnums::class))->getConstant($key);
            return $this->status == $status;
        }

        return parent::__call($name, $arguments);
    }

    private $canHandleMap = [
        'cancel' => [SetMealOrderEnums::STATUS_CREATE],
        'pay' => [SetMealOrderEnums::STATUS_CREATE],
        'confirm' => [SetMealOrderEnums::STATUS_PAY],
        'refund' => [SetMealOrderEnums::STATUS_PAY],
        // 同意退款
        'agreerefund' => [SetMealOrderEnums::STATUS_REFUND],
        'comment' => [
            SetMealOrderEnums::STATUS_CONFIRM,
            SetMealOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 售后
        'aftersale' => [
            SetMealOrderEnums::STATUS_CONFIRM,
            SetMealOrderEnums::STATUS_AUTO_CONFIRM
        ],
        // 回购
        'rebuy' => [
            SetMealOrderEnums::STATUS_CONFIRM,
            SetMealOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'delete' => [
            SetMealOrderEnums::STATUS_CANCEL,
            SetMealOrderEnums::STATUS_AUTO_CANCEL,
            SetMealOrderEnums::STATUS_ADMIN_CANCEL,
            SetMealOrderEnums::STATUS_REFUND_CONFIRM,
            SetMealOrderEnums::STATUS_CONFIRM,
            SetMealOrderEnums::STATUS_AUTO_CONFIRM
        ],
        'finish' => [
            SetMealOrderEnums::STATUS_CONFIRM,
            SetMealOrderEnums::STATUS_AUTO_CONFIRM,
        ]
    ];

    public function isHadPaid()
    {
        return !in_array($this->status, [
            SetMealOrderEnums::STATUS_CREATE,
            SetMealOrderEnums::STATUS_CANCEL,
            SetMealOrderEnums::STATUS_AUTO_CANCEL,
            SetMealOrderEnums::STATUS_ADMIN_CANCEL,
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
