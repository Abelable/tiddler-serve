<?php

namespace App\Services;

use App\Models\User;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\WxMpRegisterInput;

class UserService extends BaseService
{
    public function register($openid, WxMpRegisterInput $input)
    {
        $user = User::new();
        $user->openid = $openid;
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        $user->mobile = $input->mobile;
        $user->save();
        return $user;
    }

    public function getByOpenid($openid)
    {
        return User::query()->where('openid', $openid)->first();
    }

    public function getByMobile($mobile)
    {
        return User::query()->where('mobile', $mobile)->first();
    }

    public function getUserPage(UserPageInput $input, $columns = ['*'])
    {
        $query = User::query();
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', 'like', "%$input->nickname%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserList($columns = ['*'])
    {
        return User::query()->get($columns);
    }

    public function getNormalList($promoterIds, $columns = ['*'])
    {
        return User::query()->whereNotIn('id', $promoterIds)->get($columns);
    }

    public function getUserById($id, $columns = ['*'])
    {
        return User::query()->find($id, $columns);
    }

    public function getUserByNickname($nickname, $columns = ['*'])
    {
        return User::query()->where('nickname', $nickname)->first($columns);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return User::query()->whereIn('id', $ids)->get($columns);
    }

    public function getPageByUserIds(array $userIds, SearchPageInput $input, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function searchPage(SearchPageInput $input)
    {
        return User::search($input->keywords)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function searchList($keywords)
    {
        return User::search($keywords)->get();
    }

    public function searchUserIds($keywords)
    {
        $list = $this->searchList($keywords);
        return $list->pluck('id')->toArray();
    }

    public function searchListByUserIds(array $userIds, $keywords, $columns = ['*'])
    {
        return User::query()
            ->whereIn('id', $userIds)
            ->where(function($query) use ($keywords) {
                $query->where('nickname', 'like', "%$keywords%")
                    ->orWhere('mobile', $keywords);
            })->get($columns);
    }
}
