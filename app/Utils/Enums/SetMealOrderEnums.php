<?php

namespace App\Utils\Enums;

class SetMealOrderEnums
{
    const STATUS_CREATE = 101;
    const STATUS_CANCEL = 102;
    const STATUS_AUTO_CANCEL = 103;
    const STATUS_ADMIN_CANCEL = 104;
    const STATUS_PAY = 201;
    const STATUS_REFUND = 202;
    const STATUS_REFUND_CONFIRM = 203;
    const STATUS_CONFIRM = 301;
    const STATUS_AUTO_CONFIRM = 302;
    const STATUS_FINISHED = 401;

    const STATUS_TEXT_MAP = [
        self::STATUS_CREATE => '待付款',
        self::STATUS_CANCEL => '已取消',
        self::STATUS_AUTO_CANCEL => '已取消(系统)',
        self::STATUS_ADMIN_CANCEL => '已取消(管理员)',
        self::STATUS_PAY => '已付款',
        self::STATUS_REFUND => '订单取消，退款中',
        self::STATUS_REFUND_CONFIRM => '已退款',
        self::STATUS_CONFIRM => '已确认',
        self::STATUS_AUTO_CONFIRM => '已确认(系统)',
        self::STATUS_FINISHED => '已完成',
    ];
}
