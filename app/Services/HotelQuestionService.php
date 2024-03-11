<?php

namespace App\Services;

use App\Models\HotelQuestion;
use App\Utils\Inputs\PageInput;

class HotelQuestionService extends BaseService
{
    public function questionList($hotelId, $count, $columns = ['*'])
    {
        return HotelQuestion::query()
            ->where('hotel_id', $hotelId)
            ->orderBy('answer_num', 'desc')
            ->take($count)
            ->get($columns);
    }

    public function questionPage($hotelId, PageInput $input, $columns = ['*'])
    {
        return HotelQuestion::query()
            ->where('hotel_id', $hotelId)
            ->orderBy('answer_num', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function questionTotal($hotelId)
    {
        return HotelQuestion::query()->where('hotel_id', $hotelId)->count();
    }

    public function getQuestionById($id, $columns = ['*'])
    {
        return HotelQuestion::query()->find($id, $columns);
    }

    public function getUserQuestion($userId, $id, $columns = ['*'])
    {
        return HotelQuestion::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createQuestion($userId, $hotelId, $content)
    {
        $question = HotelQuestion::new();
        $question->user_id = $userId;
        $question->hotel_id = $hotelId;
        $question->content = $content;
        $question->save();
        return $question;
    }
}
