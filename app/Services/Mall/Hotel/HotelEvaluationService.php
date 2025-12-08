<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelEvaluation;
use App\Services\BaseService;
use App\Services\UserService;
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

    public function evaluationSummary($hotelId, $limit)
    {
        $list = $this->topEvaluationList($hotelId, $limit);
        $total = $this->getEvaluationTotal($hotelId);
        return ['list' => $list, 'total' => $total];
    }

    public function topEvaluationList($hotelId, $limit, $columns = ['*'])
    {
        $list = HotelEvaluation::query()
            ->where('hotel_id', $hotelId)
            ->orderBy('like_number', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelEvaluationList($list);
    }

    public function handelEvaluationList($evaluationList)
    {
        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        return $evaluationList->map(function (HotelEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->hotel_id);
            return $evaluation;
        });
    }

    public function getEvaluationTotal($hotelId)
    {
        return HotelEvaluation::query()->where('hotel_id', $hotelId)->count();
    }

    public function getUserEvaluation($userId, $id, $columns = ['*'])
    {
        return HotelEvaluation::query()->where('user_id', $userId)->where('id', $id)->first($columns);
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
