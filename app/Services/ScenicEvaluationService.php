<?php

namespace App\Services;

use App\Models\ScenicEvaluation;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ScenicEvaluationInput;

class ScenicEvaluationService extends BaseService
{
    public function evaluationPage($scenicId, PageInput $input, $columns = ['*'])
    {
        return ScenicEvaluation::query()
            ->where('scenic_id', $scenicId)
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
        $list = ScenicEvaluation::query()
            ->where('scenic_id', $scenicId)
            ->orderBy('like_number', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelEvaluationList($list);
    }

    public function getEvaluationTotal($scenicId)
    {
        return ScenicEvaluation::query()->where('scenic_id', $scenicId)->count();
    }

    public function handelEvaluationList($evaluationList)
    {
        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        return $evaluationList->map(function (ScenicEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->scenic_id);
            return $evaluation;
        });
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return ScenicEvaluation::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function createEvaluation($userId, array $scenicIds, ScenicEvaluationInput $input)
    {
        foreach ($scenicIds as $scenicId) {
            $evaluation = ScenicEvaluation::new();
            $evaluation->user_id = $userId;
            $evaluation->scenic_id = $scenicId;
            $evaluation->score = $input->score;
            $evaluation->content = $input->content;
            $evaluation->image_list = json_encode($input->imageList);
            $evaluation->save();

            // 更新景点评分
            $avgScore = $this->getAverageScore($scenicId);
            ScenicService::getInstance()->updateScenicAvgScore($scenicId, $avgScore);
        }
    }

    public function getAverageScore($scenicId)
    {
        return ScenicEvaluation::query()->where('scenic_id', $scenicId)->avg('score');
    }
}
