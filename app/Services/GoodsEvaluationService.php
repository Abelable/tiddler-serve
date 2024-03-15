<?php

namespace App\Services;

use App\Models\GoodsEvaluation;
use App\Utils\Inputs\GoodsEvaluationInput;
use App\Utils\Inputs\PageInput;

class GoodsEvaluationService extends BaseService
{
    public function evaluationPage($goodsId, PageInput $input, $columns = ['*'])
    {
        return GoodsEvaluation::query()
            ->where('goods_id', $goodsId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return GoodsEvaluation::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createEvaluation($userId, GoodsEvaluationInput $input)
    {
        $evaluation = GoodsEvaluation::new();
        $evaluation->user_id = $userId;
        $evaluation->goods_id = $input->goodsId;
        $evaluation->score = $input->score;
        $evaluation->content = $input->content;
        $evaluation->image_list = json_encode($input->imageList);
        $evaluation->save();
        return $evaluation;
    }

    public function getAverageScore($goodsId)
    {
        return GoodsEvaluation::query()->where('goods_id', $goodsId)->avg('score');
    }

    public function getTotalNum($goodsId)
    {
        return GoodsEvaluation::query()->where('goods_id', $goodsId)->count();
    }

    public function evaluationList($goodsId, $count, $columns = ['*'])
    {
        return GoodsEvaluation::query()
            ->where('goods_id', $goodsId)
            ->orderBy('like_number', 'desc')
            ->take($count)
            ->get($columns);
    }
}
