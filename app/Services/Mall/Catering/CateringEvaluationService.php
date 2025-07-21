<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringEvaluation;
use App\Services\BaseService;
use App\Services\UserService;
use App\Utils\Inputs\CateringEvaluationInput;
use App\Utils\Inputs\PageInput;

class CateringEvaluationService extends BaseService
{
    public function evaluationPage($restaurantId, PageInput $input, $columns = ['*'])
    {
        return CateringEvaluation::query()
            ->where('restaurant_id', $restaurantId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function evaluationSummary($scenicId, $limit)
    {
        $list = $this->topEvaluationList($scenicId, $limit);
        $total = $this->getEvaluationTotal($scenicId);
        return ['list' => $list, 'total' => $total];
    }

    public function topEvaluationList($scenicId, $limit, $columns = ['*'])
    {
        $list = CateringEvaluation::query()
            ->where('restaurant_id', $scenicId)
            ->orderBy('like_number', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelEvaluationList($list);
    }

    public function getEvaluationTotal($scenicId)
    {
        return CateringEvaluation::query()->where('restaurant_id', $scenicId)->count();
    }

    public function handelEvaluationList($evaluationList)
    {
        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        return $evaluationList->map(function (CateringEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->restaurant_id);
            return $evaluation;
        });
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return CateringEvaluation::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createEvaluation($userId, CateringEvaluationInput $input)
    {
        $evaluation = CateringEvaluation::new();
        $evaluation->user_id = $userId;
        $evaluation->restaurant_id = $input->restaurantId;
        $evaluation->score = $input->score;
        $evaluation->content = $input->content;
        $evaluation->image_list = json_encode($input->imageList);
        $evaluation->save();
        return $evaluation;
    }

    public function getAverageScore($restaurantId)
    {
        return CateringEvaluation::query()->where('restaurant_id', $restaurantId)->avg('score');
    }
}
