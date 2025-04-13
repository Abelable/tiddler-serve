<?php

namespace App\Services;

use App\Jobs\CommissionConfirm;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\Commission;
use App\Utils\CodeResponse;
use App\Utils\Enums\CommissionScene;
use App\Utils\Enums\ProductType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * C级才可以拿间推佣金
 * 团队佣金系数：C1 - 10%，C2 - 20%，C3 - 30%, 计算公式：直推/间推推广员佣金 * 佣金系数
 *
 * 场景值scene：1-自购 2-直推分享 3-间推分享 4-直推团队 5-间推团队
 * A-普通用户: user_id = 1, promoter_level = 0
 * B-推广员: user_id = 2, promoter_level = 1
 * C-推广员: user_id = 3, promoter_level = 1
 * D-推广员: user_id = 4, promoter_level = 1
 * E-C1: user_id = 5, promoter_level = 2
 *
 * 场景1「A」: 普通用户没有上级 - 不需要生成佣金记录
 *
 * 场景2「A-B/A-B-C」: 普通用户上级为推广员，没有上上级，或上上级为推广员 - 生成30%上级佣金（分享场景）
 * scene = 2 promoter_id = 2 promoter_level = 1 user_id = 1 commission_base = 100 commission_rate = 30% commission_amount = 30
 *
 * 场景3「A-B-E」: 普通用户上级为推广员，上上级为C级 - 生成30%上级佣金（分享场景）、10%上上级佣金（分享场景），生成团队佣金（直推场景）= 30%上级佣金 * 10%（C1的团队佣金系数）
 * scene = 2 promoter_id = 2 promoter_level = 1 user_id = 1 commission_base = 100 commission_rate = 30% commission_amount = 30
 * scene = 3 promoter_id = 5 promoter_level = 2 user_id = 1 commission_base = 100 commission_rate = 10% commission_amount = 10
 * scene = 4 promoter_id = 5 promoter_level = 2 user_id = 1 commission_base = 30 commission_rate = 10% commission_amount = 3
 *
 * 场景4「A-E」: 普通用户上级为C级 - 生成40%上级佣金（分享场景）
 * scene = 2 promoter_id = 5 promoter_level = 2 user_id = 1 commission_base = 100 commission_rate = 40% commission_amount = 40
 *
 * 场景5「B/B-C/B-C-D」: 推广员没有上级，或上级为推广员，且没有上上级，或上上级为推广员 - 生成30%自购佣金（自购场景）
 * scene = 1 promoter_id = 2 promoter_level = 1 user_id = 2 commission_base = 100 commission_rate = 30% commission_amount = 30
 *
 * 场景6「B-C-E」: 推广员上级为推广员，上上级为C级 - 生成30%自购佣金（自购场景）、10%上上级佣金（分享场景），生成团队佣金（间推场景）= 30%自购佣金 * 10%（C1的团队佣金系数）
 * scene = 1 promoter_id = 2 promoter_level = 1 user_id = 2 commission_base = 100 commission_rate = 30% commission_amount = 30
 * scene = 3 promoter_id = 5 promoter_level = 2 user_id = 2 commission_base = 100 commission_rate = 10% commission_amount = 10
 * scene = 5 promoter_id = 5 promoter_level = 2 user_id = 2 commission_base = 30 commission_rate = 10% commission_amount = 3
 *
 * 场景7「B-E」: 推广员上级为C级 - 生成30%自购佣金（自购场景）、10%上级佣金（分享场景），生成团队佣金（直推场景）= 30%自购佣金 * 10%（C1的团队佣金系数）
 * scene = 1 promoter_id = 2 promoter_level = 1 user_id = 2 commission_base = 100 commission_rate = 30% commission_amount = 30
 * scene = 3 promoter_id = 5 promoter_level = 2 user_id = 2 commission_base = 100 commission_rate = 10% commission_amount = 10
 * scene = 4 promoter_id = 5 promoter_level = 2 user_id = 2 commission_base = 30 commission_rate = 10% commission_amount = 3
 *
 * 场景8「E」: C级 - 生成40%自购佣金（自购场景）
 * scene = 1 promoter_id = 5 promoter_level = 2 user_id = 4 commission_base = 100 commission_rate = 40% commission_amount = 40
 */
class CommissionService extends BaseService
{
    public function createGoodsCommission(
        $orderId,
        CartGoods $cartGoods,
        $userId,
        $userLevel,
        $superiorId,
        $superiorLevel,
        $upperSuperiorId,
        $upperSuperiorLevel,
        Coupon $coupon = null
    )
    {
        $salesCommissionRate = bcdiv($cartGoods->sales_commission_rate, 100, 2);
        $promotionCommissionRate = bcdiv($cartGoods->promotion_commission_rate, 100, 2);
        $promotionCommissionUpperLimit = $cartGoods->promotion_commission_upper_limit;
        $superiorPromotionCommissionRate = bcdiv($cartGoods->superior_promotion_commission_rate, 100, 2);
        $superiorPromotionCommissionUpperLimit = $cartGoods->superior_promotion_commission_upper_limit;

        $couponDenomination = 0;
        if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
            $couponDenomination = $coupon->denomination;
        }
        $totalPrice = bcmul($cartGoods->price, $cartGoods->number, 2);
        $paymentAmount = bcsub($totalPrice, $couponDenomination, 2);
        $commissionBase = bcmul($paymentAmount, $salesCommissionRate, 2);

        $promotionCommission = bcmul($commissionBase, $promotionCommissionRate, 2);
        $finalPromotionCommission = $promotionCommissionUpperLimit != 0
            ? min($promotionCommission, $promotionCommissionUpperLimit)
            : $promotionCommission;

        $superiorPromotionCommission = bcmul($commissionBase, $superiorPromotionCommissionRate, 2);
        $finalSuperiorPromotionCommission = $superiorPromotionCommissionUpperLimit != 0
            ? min($superiorPromotionCommission, $superiorPromotionCommissionUpperLimit)
            : $superiorPromotionCommission;

        // 场景2
        if ($userLevel == 0 && $superiorLevel == 1 && $upperSuperiorLevel <= 1) {
            $this->createCommission(
                CommissionScene::DIRECT_SHARE,
                $superiorId,
                $superiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $promotionCommissionRate,
                $finalPromotionCommission,
                $promotionCommissionUpperLimit
            );
        }

        // 场景3
        if ($userLevel == 0 && $superiorLevel == 1 && $upperSuperiorLevel > 1) {
            $this->createCommission(
                CommissionScene::DIRECT_SHARE,
                $superiorId,
                $superiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $promotionCommissionRate,
                $finalPromotionCommission,
                $promotionCommissionUpperLimit
            );
            $this->createCommission(
                CommissionScene::INDIRECT_SHARE,
                $upperSuperiorId,
                $upperSuperiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $superiorPromotionCommissionRate,
                $finalSuperiorPromotionCommission,
                $superiorPromotionCommissionUpperLimit
            );

            $teamCommissionRate = bcdiv($upperSuperiorLevel - 1, 10, 2);
            $teamCommissionAmount = bcmul($finalPromotionCommission, $teamCommissionRate, 2);
            $this->createCommission(
                CommissionScene::DIRECT_TEAM,
                $upperSuperiorId,
                $upperSuperiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $finalPromotionCommission,
                $teamCommissionRate,
                $teamCommissionAmount
            );
        }

        // 场景4
        if ($userLevel == 0 && $superiorLevel > 1) {
            $commissionRate = bcadd($promotionCommissionRate, $superiorPromotionCommissionRate, 2);
            $commissionLimit = bcadd($promotionCommissionUpperLimit, $superiorPromotionCommissionUpperLimit, 2);
            $commissionAmount = bcadd($finalPromotionCommission, $finalSuperiorPromotionCommission, 2);
            $this->createCommission(
                CommissionScene::DIRECT_SHARE,
                $superiorId,
                $superiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $commissionRate,
                $commissionAmount,
                $commissionLimit
            );
        }

        // 场景5
        if ($userLevel == 1 && $superiorLevel <= 1 && $upperSuperiorLevel <= 1) {
            $this->createCommission(
                CommissionScene::SELF_PURCHASE,
                $userId,
                $userLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $promotionCommissionRate,
                $finalPromotionCommission,
                $promotionCommissionUpperLimit
            );
        }

        // 场景6
        if ($userLevel == 1 && $superiorLevel == 1 && $upperSuperiorLevel > 1) {
            $this->createCommission(
                CommissionScene::SELF_PURCHASE,
                $userId,
                $userLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $promotionCommissionRate,
                $finalPromotionCommission,
                $promotionCommissionUpperLimit
            );
            $this->createCommission(
                CommissionScene::INDIRECT_SHARE,
                $upperSuperiorId,
                $upperSuperiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $superiorPromotionCommissionRate,
                $finalSuperiorPromotionCommission,
                $superiorPromotionCommissionUpperLimit
            );

            $teamCommissionRate = bcdiv($upperSuperiorLevel - 1, 10, 2);
            $teamCommissionAmount = bcmul($finalPromotionCommission, $teamCommissionRate, 2);
            $this->createCommission(
                CommissionScene::INDIRECT_TEAM,
                $upperSuperiorId,
                $upperSuperiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $finalPromotionCommission,
                $teamCommissionRate,
                $teamCommissionAmount
            );
        }

        // 场景7
        if ($userLevel == 1 && $superiorLevel > 1) {
            $this->createCommission(
                CommissionScene::SELF_PURCHASE,
                $userId,
                $userLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $promotionCommissionRate,
                $finalPromotionCommission,
                $promotionCommissionUpperLimit
            );
            $this->createCommission(
                CommissionScene::INDIRECT_SHARE,
                $superiorId,
                $superiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $superiorPromotionCommissionRate,
                $finalSuperiorPromotionCommission,
                $superiorPromotionCommissionUpperLimit
            );

            $teamCommissionRate = bcdiv($superiorLevel - 1, 10, 2);
            $teamCommissionAmount = bcmul($finalPromotionCommission, $teamCommissionRate, 2);
            $this->createCommission(
                CommissionScene::DIRECT_TEAM,
                $superiorId,
                $superiorLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $finalPromotionCommission,
                $teamCommissionRate,
                $teamCommissionAmount
            );
        }

        // 场景8
        if ($userLevel > 1) {
            $commissionRate = bcadd($promotionCommissionRate, $superiorPromotionCommissionRate, 2);
            $commissionLimit = bcadd($promotionCommissionUpperLimit, $superiorPromotionCommissionUpperLimit, 2);
            $commissionAmount = bcadd($finalPromotionCommission, $finalSuperiorPromotionCommission, 2);
            $this->createCommission(
                CommissionScene::SELF_PURCHASE,
                $userId,
                $userLevel,
                $userId,
                $orderId,
                ProductType::GOODS,
                $cartGoods->goods_id,
                $commissionBase,
                $commissionRate,
                $commissionAmount,
                $commissionLimit
            );
        }
    }

    public function createCommission(
        $scene,
        $promoterId,
        $promoterLevel,
        $userId,
        $orderId,
        $productType,
        $productId,
        $commissionBase,
        $commissionRate,
        $commissionAmount,
        $commissionLimit = 0
    )
    {
        $commission = Commission::new();
        $commission->scene = $scene;
        $commission->promoter_id = $promoterId;
        $commission->promoter_level = $promoterLevel;
        $commission->user_id = $userId;
        $commission->order_id = $orderId;
        $commission->product_type = $productType;
        $commission->product_id = $productId;
        $commission->commission_base = $commissionBase;
        $commission->commission_rate = $commissionRate;
        $commission->commission_amount = $commissionAmount;
        $commission->commission_limit = $commissionLimit;
        $commission->save();
        return $commission;
    }

    public function updateListToOrderPaidStatus(array $orderIds)
    {
        $commissionList = $this->getUnpaidListByOrderIds($orderIds);
        return $commissionList->map(function (Commission $commission) {
            $commission->status = 1;
            $commission->save();

            // 更新推广员商品佣金
            if ($commission->scene == 1) {
                PromoterService::getInstance()->updateCommissionSum($commission->user_id, $commission->commission_amount);
            } else {
                PromoterService::getInstance()->updateCommissionSum($commission->superior_id, $commission->commission_amount);
            }

            return $commission;
        });
    }

    public function deleteUnpaidListByOrderIds(array $orderIds)
    {
        return Commission::query()->where('status', 0)->whereIn('order_id', $orderIds)->delete();
    }

    public function getUnpaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->where('status', 0)
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function updateListToOrderConfirmStatus($orderIds, $role = 'user')
    {
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        return $commissionList->map(function (Commission $commission) use ($role) {
            if ($commission->refund_status == 1 && $role == 'user') {
                // 7天无理由商品：确认收货7天后更新佣金状态
                dispatch(new CommissionConfirm($commission->id));
            } else {
                $commission->status = 2;
                $commission->save();
            }
            return $commission;
        });
    }

    public function updateToOrderConfirmStatus($id)
    {
        $commission = $this->getCommissionById($id);
        if (is_null($commission)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '佣金记录不存在或已删除');
        }
        $commission->status = 2;
        $commission->save();
        return $commission;
    }

    public function deletePaidListByOrderIds(array $orderIds)
    {
        $commissionList = $this->getPaidListByOrderIds($orderIds);
        $commissionList->map(function (Commission $commission) {
            // 更新推广员商品佣金
            if ($commission->scene == 1) {
                PromoterService::getInstance()->updateCommissionSum($commission->user_id, -$commission->commission_amount);
            } else {
                PromoterService::getInstance()->updateCommissionSum($commission->superior_id, -$commission->commission_amount);
            }

            $commission->delete();
        });
    }

    public function deletePaidCommission($orderId, $goodsId)
    {
        $commission = Commission::query()
            ->where('status', 1)
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->first();

        // 更新推广员商品佣金
        if ($commission->scene == 1) {
            PromoterService::getInstance()->updateCommissionSum($commission->user_id, -$commission->commission_amount);
        } else {
            PromoterService::getInstance()->updateCommissionSum($commission->superior_id, -$commission->commission_amount);
        }

        $commission->delete();
    }

    public function getPaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return Commission::query()
            ->where('status', 1)
            ->whereIn('order_id', $orderIds)
            ->get($columns);
    }

    public function getCommissionById($id, $columns = ['*'])
    {
        return Commission::query()->find($id, $columns);
    }
    public function getCommissionListByIds(array $ids, $columns = ['*'])
    {
        return Commission::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserCommissionById($userId, $id, $columns = ['*'])
    {
        return Commission::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserCommissionList($userId, $ids, $columns = ['*'])
    {
        return Commission::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function getUserCommissionSum($userId, $statusList)
    {
        return $this->getUserCommissionQuery([$userId], $statusList)->sum('commission_amount');
    }

    public function getUserGMV(array $userIds, $statusList)
    {
        return $this->getUserCommissionQuery($userIds, $statusList)->sum('commission_base');
    }

    public function getUserCommissionQuery(array $userIds, array $statusList)
    {
        return Commission::query()
            ->where(function($query) use ($userIds) {
                $query->where(function($query) use ($userIds) {
                    $query->where('scene', 1)
                        ->whereIn('user_id', $userIds);
                })->orWhere(function($query) use ($userIds) {
                    $query->where('scene', 2)
                        ->whereIn('superior_id', $userIds);
                });
            })->whereIn('status', $statusList);
    }

    public function getUserCommissionListByTimeType($userId, $timeType, array $statusList, $scene = null, $columns = ['*'])
    {
        $query = $this->getUserCommissionQueryByTimeType([$userId], $timeType, $scene);
        return $query->whereIn('status', $statusList)->get($columns);
    }

    /**
     * @param array $userIds
     * @param $timeType
     * @param $scene
     * @return Commission|\Illuminate\Database\Eloquent\Builder
     */
    public function getUserCommissionQueryByTimeType(array $userIds, $timeType, $scene = null)
    {
        $query = Commission::query();

        if (!is_null($scene)) {
            if ($scene == 1) {
                $query = $query->whereIn('user_id', $userIds);
            } else {
                $query = $query->whereIn('superior_id', $userIds);
            }
            $query = $query->where('scene', $scene);
        } else {
            $query = $query->where(function($query) use ($userIds) {
                $query->where(function($query) use ($userIds) {
                    $query->where('scene', 1)
                        ->whereIn('user_id', $userIds);
                })->orWhere(function($query) use ($userIds) {
                    $query->where('scene', 2)
                        ->whereIn('superior_id', $userIds);
                });
            });
        }

        switch ($timeType) {
            case 1:
                $query = $query->whereDate('created_at', Carbon::today());
                break;
            case 2:
                $query = $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 3:
                $query = $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
                break;
            case 4:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                break;
            case 5:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonths(2)->startOfMonth(), Carbon::now()->subMonths(2)->endOfMonth()]);
                break;
        }
        return $query;
    }

    public function getSettledCommissionListByUserIds(array $userIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('status', [2, 3, 4])
            ->get($columns);
    }

    public function getSettledCommissionListBySuperiorIds(array $superiorIds, $columns = ['*'])
    {
        return Commission::query()
            ->whereIn('superior_id', $superiorIds)
            ->whereIn('status', [2, 3, 4])
            ->get($columns);
    }

    public function getUserGMVByTimeType($userId, $timeType)
    {
        return $this->getUserCommissionQueryByTimeType([$userId], $timeType)->whereIn('status', [2, 3, 4])->sum('commission_base');
    }

    public function withdrawUserCommission($userId, $scene, $withdrawalId)
    {
        $commissionList = $this->getUserCommissionQuery([$userId], [2])
            ->where('scene', $scene)
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->withdrawal_id = $withdrawalId;
            $commission->status = 3;
            $commission->save();
        }
    }

    public function restoreCommissionByWithdrawalId($withdrawalId)
    {
        $commissionList = Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', 3)->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->status = 2;
            $commission->save();
        }
    }

    public function settleCommissionToBalance($userId, $scene, $withdrawalId)
    {
        $commissionList = $this->getUserCommissionQuery([$userId], [2])
            ->where('scene', $scene)
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->withdrawal_id = $withdrawalId;
            $commission->status = 4;
            $commission->save();
        }
    }

    public function getCommissionSumByWithdrawalId($withdrawalId, $status = 3)
    {
        return Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', $status)->sum('commission_amount');
    }

    public function settleCommissionByWithdrawalId($withdrawalId)
    {
        $commissionList = Commission::query()->where('withdrawal_id', $withdrawalId)->where('status', 3)->get();
        /** @var Commission $commission */
        foreach ($commissionList as $commission) {
            $commission->status = 4;
            $commission->save();
        }
    }

    public function monthlyCommissionList()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        return Commission::query()
            ->whereIn('status', [1, 2, 3, 4])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(commission_amount) as sum")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    public function getCommissionSumByStatus(array $statusList)
    {
        return Commission::query()->whereIn('status', $statusList)->sum('commission_amount');
    }
}
