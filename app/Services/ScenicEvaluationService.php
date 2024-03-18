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

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return ScenicEvaluation::query()->where('user_id', $userId)->find($id, $columns);
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
        }
    }

    public function getAverageScore($goodsId)
    {
        return ScenicEvaluation::query()->where('goods_id', $goodsId)->avg('score');
    }
}
