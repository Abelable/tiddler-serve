<?php

namespace App\Services;

use App\Models\ScenicQuestion;
use App\Utils\Inputs\PageInput;

class ScenicQuestionService extends BaseService
{
    public function questionList($scenicId, $count, $columns = ['*'])
    {
        return ScenicQuestion::query()
            ->where('scenic_id', $scenicId)
            ->orderBy('answer_num', 'desc')
            ->take($count)
            ->get($columns);
    }

    public function questionPage($scenicId, PageInput $input, $columns = ['*'])
    {
        return ScenicQuestion::query()
            ->where('scenic_id', $scenicId)
            ->orderBy('answer_num', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function questionTotal($hotelId)
    {
        return ScenicQuestion::query()->where('hotel_id', $hotelId)->count();
    }

    public function getQuestionById($id, $columns = ['*'])
    {
        return ScenicQuestion::query()->find($id, $columns);
    }

    public function getUserQuestion($userId, $id, $columns = ['*'])
    {
        return ScenicQuestion::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createQuestion($userId, $scenicId, $content)
    {
        $question = ScenicQuestion::new();
        $question->user_id = $userId;
        $question->scenic_id = $scenicId;
        $question->content = $content;
        $question->save();
        return $question;
    }
}
