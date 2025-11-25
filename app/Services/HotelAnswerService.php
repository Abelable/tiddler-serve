<?php

namespace App\Services;

use App\Models\HotelAnswer;
use App\Utils\Inputs\PageInput;

class HotelAnswerService extends BaseService
{
    public function answerPage($questionId, PageInput $input, $columns = ['*'])
    {
        return HotelAnswer::query()
            ->where('question_id', $questionId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserAnswer($userId, $id, $columns = ['*'])
    {
        return HotelAnswer::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function getUserAnswerByQuestionId($userId, $questionId, $columns = ['*'])
    {
        return HotelAnswer::query()->where('user_id', $userId)->where('question_id', $questionId)->first($columns);
    }

    public function createAnswer($userId, $questionId, $content)
    {
        $answer = HotelAnswer::new();
        $answer->user_id = $userId;
        $answer->question_id = $questionId;
        $answer->content = $content;
        $answer->save();
        return $answer;
    }

    public function getListByQuestionIds(array $questionIds, $columns = ['*'])
    {
        return HotelAnswer::query()->whereIn('question_id', $questionIds)->get($columns);
    }
}
