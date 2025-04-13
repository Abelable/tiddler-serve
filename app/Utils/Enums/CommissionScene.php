<?php

namespace App\Utils\Enums;

class CommissionScene
{
    const SELF_PURCHASE = 1;
    const DIRECT_SHARE = 2;
    const INDIRECT_SHARE = 3;
    const DIRECT_TEAM = 4;
    const INDIRECT_TEAM = 5;

    const STATUS_TEXT_MAP = [
        self::SELF_PURCHASE => '自购',
        self::DIRECT_SHARE => '直推分享',
        self::INDIRECT_SHARE => '间推分享',
        self::DIRECT_TEAM => '直推团队',
        self::INDIRECT_TEAM => '间推团队',
    ];
}
