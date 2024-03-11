<?php

namespace App\Services;

use App\Models\ScenicQuestion;
use App\Utils\Inputs\PageInput;

class ScenicQuestionService extends BaseService
{
    public function questionPage($questionId, PageInput $input, $columns = ['*'])
    {
        return ScenicQuestion::query()
            ->where('question_id', $questionId)
            ->orderBy('answer_num', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
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
