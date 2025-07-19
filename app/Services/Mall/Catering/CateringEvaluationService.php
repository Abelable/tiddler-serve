<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringEvaluation;
use App\Services\BaseService;
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
