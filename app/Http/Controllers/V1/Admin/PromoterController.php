<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promoter\Promoter;
use App\Models\User;
use App\Services\Mall\CommissionService;
use App\Services\Mall\CommissionWithdrawalService;
use App\Services\Promoter\PromoterChangeLogService;
use App\Services\Promoter\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PromoterController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var UserPageInput $input */
        $input = UserPageInput::new();

        if (!empty($input->nickname) || !empty($input->mobile)) {
            $page = UserService::getInstance()->getUserPage($input);
            $userList = collect($page->items());
            $userIds = $userList->pluck('id')->toArray();
            $promoterList = PromoterService::getInstance()->getPromoterListByUserIds($userIds)->keyBy('user_id');
            $withdrawSumList = CommissionWithdrawalService::getInstance()->getWithdrawSumListByUserIds($userIds)->keyBy('user_id');
            $list = $userList->map(function (User $user) use ($withdrawSumList, $promoterList) {
                $promoter = $promoterList->get($user->id);
                if (!is_null($promoter)) {
                    $promoter['avatar'] = $user->avatar;
                    $promoter['nickname'] = $user->nickname;
                    $promoter['mobile'] = $user->mobile;

                    $withdrawSum = $withdrawSumList->get($user->id);
                    $promoter['settledCommissionSum'] = $withdrawSum ? $withdrawSum->sum : 0;
                }

                return $promoter;
            })->filter(function ($promoter) {
                return !is_null($promoter);
            })->values();
        } else {
            $page = PromoterService::getInstance()->getPromoterPage($input);
            $promoterList = collect($page->items());

            $userIds = $promoterList->pluck('user_id')->toArray();
            $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname', 'mobile'])->keyBy('id');
            $withdrawSumList = CommissionWithdrawalService::getInstance()->getWithdrawSumListByUserIds($userIds)->keyBy('user_id');

            $list = $promoterList->map(function (Promoter $promoter) use ($withdrawSumList, $userList) {
                /** @var User $user */
                $user = $userList->get($promoter->user_id);
                $promoter['avatar'] = $user->avatar;
                $promoter['nickname'] = $user->nickname;
                $promoter['mobile'] = $user->mobile;

                $withdrawSum = $withdrawSumList->get($user->id);
                $promoter['settledCommissionSum'] = $withdrawSum ? $withdrawSum->sum : 0;

                return $promoter;
            });
        }

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代言人不存在');
        }
        return $this->success($promoter);
    }

    public function add()
    {
        $userId = $this->verifyRequiredId('userId');
        $level = $this->verifyRequiredInteger('level');
        $scene = $this->verifyRequiredInteger('scene');
        $duration = $this->verifyRequiredInteger('duration');

        $promoter = PromoterService::getInstance()->getPromoterByUserId($userId);
        if (!is_null($promoter)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '代言人已存在，请勿重复添加');
        }

        DB::transaction(function () use ($duration, $userId, $level, $scene) {
            $promoter = PromoterService::getInstance()->adminCreate($userId, $level, $scene, $duration);

            PromoterChangeLogService::getInstance()
                ->createLog($promoter->id, 1, 0, $level);

            $superiorId = RelationService::getInstance()->getSuperiorId($userId);
            if ($superiorId) {
                PromoterService::getInstance()->updateSubPromoterCount($superiorId);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $level = $this->verifyRequiredInteger('level');
        $scene = $this->verifyRequiredInteger('scene');
        $duration = $this->verifyInteger('duration');

        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代言人不存在');
        }

        DB::transaction(function () use ($duration, $promoter, $level, $scene) {
            if ($promoter->level != $level) {
                $promoter->level = $level;
                $promoter->scene = $scene;
            }

            if ($duration) {
                $expirationTime = Carbon::parse($promoter->expiration_time)
                    ->addDays($duration)
                    ->setTimezone('UTC')
                    ->format('Y-m-d\TH:i:s.v\Z');
                $promoter->expiration_time = $expirationTime;
                if ($promoter->status == 2) {
                    $promoter->status == 1;
                }
            }

            $promoter->save();
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $promoter = PromoterService::getInstance()->getPromoterById($id);
        if (is_null($promoter)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代言人不存在');
        }
        DB::transaction(function () use ($promoter) {
            $promoter->delete();

            // 删除上下级关系
            RelationService::getInstance()->deleteBySuperiorId($promoter->user_id);
        });

        return $this->success();
    }

    public function options()
    {
        $promoterOptions = PromoterService::getInstance()->getOptions();
        $userIds = $promoterOptions->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds)->keyBy('id');
        $options = $promoterOptions->map(function (Promoter $promoter) use ($userList) {
            /** @var User $userInfo */
            $userInfo = $userList->get($promoter->user_id);
            return [
                'id' => $userInfo->id,
                'nickname' => $userInfo->nickname,
                'avatar' => $userInfo->avatar,
                'level' => $promoter->level,
            ];
        });
        return $this->success($options);
    }

    public function topPromoterList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = PromoterService::getInstance()->getTopPromoterPage($input);
        $promoterList = collect($page->items());

        $userIds = $promoterList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname', 'mobile'])->keyBy('id');

        $withdrawSumList = CommissionWithdrawalService::getInstance()->getWithdrawSumListByUserIds($userIds)->keyBy('user_id');

        $list = $promoterList->map(function (Promoter $promoter, $index) use ($withdrawSumList, $page, $userList) {
            $user = $userList->get($promoter->user_id);
            $promoter['avatar'] = $user->avatar;
            $promoter['nickname'] = $user->nickname;
            $promoter['mobile'] = $user->mobile;

            $promoter['rank'] = ($page->currentPage() - 1) * $page->perPage() + $index + 1;

            $withdrawSum = $withdrawSumList->get($user->id);
            $promoter['settledCommissionSum'] = $withdrawSum ? $withdrawSum->sum : 0;

            return $promoter;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function updateList()
    {
        $promoterList = Promoter::query()->get();
        $promoterList->map(function (Promoter $promoter) {
            $promotedUserCount = RelationService::getInstance()->getCountBySuperiorId($promoter->user_id);
            $promoter->promoted_user_number = $promotedUserCount;

            $commissionSum = CommissionService::getInstance()->getUserCommissionSum($promoter->user_id, [1, 2, 3, 4]);
            $promoter->commission_sum = $commissionSum;

            $teamCommissionSum = TeamCommissionService::getInstance()->getUserCommission($promoter->user_id, [1, 2, 3, 4]);
            $promoter->team_commission_sum = $teamCommissionSum;

            $promoter->save();
        });
        return $this->success();
    }
}
