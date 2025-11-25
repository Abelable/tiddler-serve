<?php

namespace App\Services;

use App\Models\PromoterEvaluation;
use App\Utils\Inputs\PromoterEvaluationInput;
use App\Utils\Inputs\PageInput;

class PromoterEvaluationService extends BaseService
{
    public function evaluationPage($promoterId, PageInput $input, $columns = ['*'])
    {
        return PromoterEvaluation::query()
            ->where('promoter_id', $promoterId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return PromoterEvaluation::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function createEvaluation($userId, PromoterEvaluationInput $input)
    {
        $evaluation = PromoterEvaluation::new();
        $evaluation->user_id = $userId;
        $evaluation->promoter_id = $input->promoterId;
        $evaluation->score = $input->score;
        $evaluation->content = $input->content;
        $evaluation->image_list = json_encode($input->imageList);
        $evaluation->save();

        return $evaluation;
    }

    public function getAverageScore($promoterId)
    {
        return PromoterEvaluation::query()->where('promoter_id', $promoterId)->avg('score');
    }

    public function getTotalNum($promoterId)
    {
        return PromoterEvaluation::query()->where('promoter_id', $promoterId)->count();
    }

    public function evaluationList($promoterId, $count, $columns = ['*'])
    {
        return PromoterEvaluation::query()
            ->where('promoter_id', $promoterId)
            ->take($count)
            ->get($columns);
    }
}
