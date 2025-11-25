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
        return GoodsEvaluation::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function getEvaluationByOrderId($orderId, $columns = ['*'])
    {
        return GoodsEvaluation::query()->where('order_id', $orderId)->first($columns);
    }

    public function createEvaluation($userId, GoodsEvaluationInput $input)
    {
        foreach ($input->goodsIds as $goodsId) {
            $evaluation = GoodsEvaluation::new();
            $evaluation->user_id = $userId;
            $evaluation->goods_id = $goodsId;
            $evaluation->score = $input->score;
            $evaluation->content = $input->content;
            $evaluation->image_list = json_encode($input->imageList);
            $evaluation->save();
        }
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
