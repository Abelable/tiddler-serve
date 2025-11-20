<?php

namespace App\Utils\Enums;

class NotificationType
{
    const MERCHANT_INFO_REVIEW = 1; // 商家入驻审核（景点、酒店、餐饮、商品）
    const AUTH_INFO_REVIEW = 2; // 实名认证信息审核
    const WITHDRAWAL_REVIEW = 3; // 提现审核（佣金、商家收益、奖励）
    const SHOP_WITHDRAWAL_REVIEW = 4; // 退店审核
    const SCENIC_SPOT_REVIEW = 5; // 景点认领审核
    const SCENIC_TICKET_REVIEW = 5; // 景点门票审核


    // 1**: 管理后台通知
    // 10*: 商家入驻审核通知
    const SCENIC_MERCHANT_REVIEW = 101;
    const HOTEL_MERCHANT_REVIEW = 102;
    const CATERING_MERCHANT_REVIEW = 103;
    const GOODS_MERCHANT_REVIEW = 104;

    // 2**: 用户通知
    // 20*: 商家入驻审核通过
    const SCENIC_MERCHANT_APPROVED = 201;
    const HOTEL_MERCHANT_APPROVED = 202;
    const CATERING_MERCHANT_APPROVED = 203;
    const GOODS_MERCHANT_APPROVED = 204;
}
