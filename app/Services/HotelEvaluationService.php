<?php

namespace App\Services;

use App\Models\HotelEvaluation;
use App\Utils\Inputs\HotelEvaluationInput;
use App\Utils\Inputs\PageInput;

class HotelEvaluationService extends BaseService
{
    public function evaluationPage($hotelId, PageInput $input, $columns = ['*'])
    {
        return HotelEvaluation::query()
            ->where('hotel_id', $hotelId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return HotelEvaluation::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createEvaluation($userId, HotelEvaluationInput $input)
    {
        $evaluation = HotelEvaluation::new();
        $evaluation->user_id = $userId;
        $evaluation->hotel_id = $input->hotelId;
        $evaluation->score = $input->score;
        $evaluation->content = $input->content;
        $evaluation->image_list = json_encode($input->imageList);
        $evaluation->save();
        return $evaluation;
    }

    public function getAverageScore($hotelId)
    {
        return HotelEvaluation::query()->where('hotel_id', $hotelId)->avg('score');
    }
}
