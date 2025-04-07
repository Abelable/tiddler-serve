<?php

namespace App\Services;

use App\Models\Relation;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;

class RelationService extends BaseService
{
    public function banding($superiorId, $userId)
    {
        $relation = $this->getRelation($superiorId, $userId);
        if (!is_null($relation)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '上下级关系已存在');
        }

        $relation = Relation::new();
        $relation->superior_id = $superiorId;
        $relation->user_id = $userId;
        $relation->save();
        return $relation;
    }

    public function getRelation($superiorId, $userId, $columns = ['*'])
    {
        return Relation::query()->where('superior_id', $superiorId)->where('user_id', $userId)->first($columns);
    }

    public function getRelationByFanId($userId, $columns = ['*'])
    {
        return Relation::query()->where('user_id', $userId)->first($columns);
    }

    public function getListByFanIds(array $userIds, $columns = ['*'])
    {
        return Relation::query()->whereIn('user_id', $userIds)->get($columns);
    }

    public function getListBySuperiorId($superiorId, $columns = ['*'])
    {
        return Relation::query()->where('superior_id', $superiorId)->get($columns);
    }

    public function getRelationListBySuperiorIds(array $superiorIds, $columns = ['*'])
    {
        return Relation::query()->whereIn('superior_id', $superiorIds)->get($columns);
    }

    public function getCountBySuperiorId($superiorId)
    {
        return Relation::query()->where('superior_id', $superiorId)->count();
    }

    public function getTodayCountBySuperiorId($superiorId)
    {
        return Relation::query()->whereDate('created_at', Carbon::today())->where('superior_id', $superiorId)->count();
    }

    public function getTodayListBySuperiorId($superiorId, $columns = ['*'])
    {
        return Relation::query()->whereDate('created_at', Carbon::today())->where('superior_id', $superiorId)->get($columns);
    }

    public function getSuperiorId($userId, $columns = ['*'])
    {
        $relation = Relation::query()->where('user_id', $userId)->first($columns);
        return $relation->superior_id ?? null;
    }

    public function deleteBySuperiorId($superiorId)
    {
        return Relation::query()->where('superior_id', $superiorId)->delete();
    }
}
