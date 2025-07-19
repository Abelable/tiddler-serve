<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringAnswer;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class CateringAnswerService extends BaseService
{
    public function answerPage($questionId, PageInput $input, $columns = ['*'])
    {
        return CateringAnswer::query()
            ->where('question_id', $questionId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserAnswer($userId, $id, $columns = ['*'])
    {
        return CateringAnswer::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserAnswerByQuestionId($userId, $questionId, $columns = ['*'])
    {
        return CateringAnswer::query()->where('user_id', $userId)->where('question_id', $questionId)->first($columns);
    }

    public function createAnswer($userId, $questionId, $content)
    {
        $answer = CateringAnswer::new();
        $answer->user_id = $userId;
        $answer->question_id = $questionId;
        $answer->content = $content;
        $answer->save();
        return $answer;
    }
}
