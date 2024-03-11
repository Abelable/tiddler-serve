<?php

namespace App\Services;

use App\Models\CateringQuestion;
use App\Utils\Inputs\PageInput;

class CateringQuestionService extends BaseService
{
    public function questionList($restaurantId, $count, $columns = ['*'])
    {
        return CateringQuestion::query()
            ->where('restaurant_id', $restaurantId)
            ->orderBy('answer_num', 'desc')
            ->take($count)
            ->get($columns);
    }

    public function questionPage($restaurantId, PageInput $input, $columns = ['*'])
    {
        return CateringQuestion::query()
            ->where('restaurant_id', $restaurantId)
            ->orderBy('answer_num', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function questionTotal($restaurantId)
    {
        return CateringQuestion::query()->where('restaurant_id', $restaurantId)->count();
    }

    public function getQuestionById($id, $columns = ['*'])
    {
        return CateringQuestion::query()->find($id, $columns);
    }

    public function getUserQuestion($userId, $id, $columns = ['*'])
    {
        return CateringQuestion::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createQuestion($userId, $restaurantId, $content)
    {
        $question = CateringQuestion::new();
        $question->user_id = $userId;
        $question->restaurant_id = $restaurantId;
        $question->content = $content;
        $question->save();
        return $question;
    }
}
