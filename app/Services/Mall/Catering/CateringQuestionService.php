<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\CateringAnswer;
use App\Models\Mall\Catering\CateringQuestion;
use App\Services\BaseService;
use App\Services\UserService;
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

    public function qaSummary($scenicId, $limit)
    {
        $list = $this->topQuestionList($scenicId, $limit);
        $total = $this->questionTotal($scenicId);
        return ['list' => $list, 'total' => $total];
    }

    public function topQuestionList($scenicId, $limit, $columns = ['*'])
    {
        $list = CateringQuestion::query()
            ->where('restaurant_id', $scenicId)
            ->orderBy('answer_num', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelQuestionList($list);
    }

    public function questionTotal($scenicId)
    {
        return CateringQuestion::query()->where('restaurant_id', $scenicId)->count();
    }

    public function handelQuestionList($questionList)
    {
        return $questionList->map(function (CateringQuestion $question, $index) {
            if ($index == 0) {
                /** @var CateringAnswer $firstAnswer */
                $firstAnswer = $question->firstAnswer();
                if (!is_null($firstAnswer)) {
                    $userInfo = UserService::getInstance()
                        ->getUserById($firstAnswer->user_id, ['id', 'avatar', 'nickname']);
                    $firstAnswer['userInfo'] = $userInfo;
                    unset($firstAnswer->user_id);
                }
                return [
                    'content' => $question->content,
                    'firstAnswer' => $firstAnswer,
                ];
            } else {
                $userIds = $question->answerList->pluck('user_id')->toArray();
                $userList = UserService::getInstance()
                    ->getListByIds(array_slice($userIds, 0, 3), ['id', 'avatar']);
                return [
                    'content' => $question->content,
                    'answerNum' => $question->answer_num,
                    'userList' => $userList,
                ];
            }
        });
    }

    public function questionPage($restaurantId, PageInput $input, $columns = ['*'])
    {
        return CateringQuestion::query()
            ->where('restaurant_id', $restaurantId)
            ->orderBy('answer_num', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getQuestionById($id, $columns = ['*'])
    {
        return CateringQuestion::query()->find($id, $columns);
    }

    public function getUserQuestion($userId, $id, $columns = ['*'])
    {
        return CateringQuestion::query()->where('user_id', $userId)->where('id', $id)->first($columns);
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
