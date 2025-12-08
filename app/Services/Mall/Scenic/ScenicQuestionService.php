<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\ScenicAnswer;
use App\Models\Mall\Scenic\ScenicQuestion;
use App\Services\BaseService;
use App\Services\UserService;
use App\Utils\Inputs\PageInput;

class ScenicQuestionService extends BaseService
{
    public function qaSummary($scenicId, $limit)
    {
        $list = $this->topQuestionList($scenicId, $limit);
        $total = $this->questionTotal($scenicId);
        return ['list' => $list, 'total' => $total];
    }

    public function topQuestionList($scenicId, $limit, $columns = ['*'])
    {
        $list = ScenicQuestion::query()
            ->where('scenic_id', $scenicId)
            ->orderBy('answer_num', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelQuestionList($list);
    }

    public function questionTotal($scenicId)
    {
        return ScenicQuestion::query()->where('scenic_id', $scenicId)->count();
    }

    public function handelQuestionList($questionList)
    {
        return $questionList->map(function (ScenicQuestion $question, $index) {
            if ($index == 0) {
                /** @var ScenicAnswer $firstAnswer */
                $firstAnswer = $question->firstAnswer();
                if (!is_null($firstAnswer)) {
                    $userInfo = UserService::getInstance()->getUserById($firstAnswer->user_id, ['id', 'avatar', 'nickname']);
                    $firstAnswer['userInfo'] = $userInfo;
                    unset($firstAnswer->user_id);
                }
                return [
                    'content' => $question->content,
                    'firstAnswer' => $firstAnswer,
                ];
            } else {
                $userIds = $question->answerList->pluck('user_id')->toArray();
                $userList = UserService::getInstance()->getListByIds(array_slice($userIds, 0, 3), ['id', 'avatar']);
                return [
                    'content' => $question->content,
                    'answerNum' => $question->answer_num,
                    'userList' => $userList,
                ];
            }
        });
    }

    public function questionPage($scenicId, PageInput $input, $columns = ['*'])
    {
        return ScenicQuestion::query()
            ->where('scenic_id', $scenicId)
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
        return ScenicQuestion::query()->where('user_id', $userId)->where('id', $id)->first($columns);
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
