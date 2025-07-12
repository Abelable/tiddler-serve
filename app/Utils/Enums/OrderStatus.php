<?php

namespace App\Utils\Enums;

class OrderStatus
{
    const CREATED = 101;
    const CANCELED = 102;
    const AUTO_CANCELED = 103;
    const ADMIN_CANCELED = 104;
    const PAID = 201;
    const EXPORTED = 202;
    const REFUNDING = 203;
    const REFUNDED = 204;
    const SHIPPED = 301;
    const PENDING_VERIFICATION = 302;
    const CONFIRMED = 401;
    const AUTO_CONFIRMED = 402;
    const ADMIN_CONFIRMED = 403;
    const FINISHED = 501;
    const AUTO_FINISHED = 502;

    const TEXT_MAP = [
        self::CREATED => '已创建，待付款',
        self::CANCELED => '已取消（用户）',
        self::AUTO_CANCELED => '已取消（系统）',
        self::ADMIN_CANCELED => '已取消（管理员）',
        self::PAID => '已付款',
        self::EXPORTED => '已付款，订单已导出',
        self::REFUNDING => '退款中',
        self::REFUNDED => '已退款',
        self::SHIPPED => '已发货',
        self::PENDING_VERIFICATION => '待核销',
        self::CONFIRMED => '已确认（用户）',
        self::AUTO_CONFIRMED => '已确认（系统）',
        self::ADMIN_CONFIRMED => '已确认（管理员）',
        self::FINISHED => '已完成',
        self::AUTO_FINISHED => '已完成(系统)',
    ];
}
