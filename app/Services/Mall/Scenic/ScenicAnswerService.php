<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\ScenicAnswer;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ScenicAnswerService extends BaseService
{
    public function answerPage($questionId, PageInput $input, $columns = ['*'])
    {
        return ScenicAnswer::query()
            ->where('question_id', $questionId)
            ->orderBy('like_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserAnswer($userId, $id, $columns = ['*'])
    {
        return ScenicAnswer::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function getUserAnswerByQuestionId($userId, $questionId, $columns = ['*'])
    {
        return ScenicAnswer::query()->where('user_id', $userId)->where('question_id', $questionId)->first($columns);
    }

    public function createAnswer($userId, $questionId, $content)
    {
        $answer = ScenicAnswer::new();
        $answer->user_id = $userId;
        $answer->question_id = $questionId;
        $answer->content = $content;
        $answer->save();
        return $answer;
    }
}
