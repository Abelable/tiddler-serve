<?php

namespace App\Utils\Enums;

class MealTicketOrderStatus
{
    const CREATED = 101;
    const CANCELED = 102;
    const AUTO_CANCELED = 103;
    const ADMIN_CANCELED = 104;
    const PAID = 201;
    const REFUNDING = 202;
    const REFUNDED = 203;
    const MERCHANT_REJECTED = 204;

    const MERCHANT_APPROVED = 301;
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
        self::PAID => '已付款，待商家确认',
        self::REFUNDING => '退款中（用户）',
        self::REFUNDED => '已退款',
        self::MERCHANT_REJECTED => '商家驳回，已退款',
        self::MERCHANT_APPROVED => '商家已确认，待使用',
        self::CONFIRMED => '已确认（用户）',
        self::AUTO_CONFIRMED => '已确认（系统）',
        self::ADMIN_CONFIRMED => '已确认（管理员）',
        self::FINISHED => '已完成',
        self::AUTO_FINISHED => '已完成（系统）',
    ];
}
