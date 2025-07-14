<?php

namespace App\Services;

use App\Models\HotelAnswer;
use App\Models\HotelQuestion;
use App\Utils\Inputs\PageInput;

class HotelQuestionService extends BaseService
{
    public function qaSummary($hotelId, $limit)
    {
        $list = $this->topQuestionList($hotelId, $limit);
        $total = $this->questionTotal($hotelId);
        return ['list' => $list, 'total' => $total];
    }

    public function topQuestionList($hotelId, $limit, $columns = ['*'])
    {
        $list = HotelQuestion::query()
            ->where('hotel_id', $hotelId)
            ->orderBy('answer_num', 'desc')
            ->take($limit)
            ->get($columns);
        return $this->handelQuestionList($list);
    }

    public function handelQuestionList($questionList)
    {
        return $questionList->map(function (HotelQuestion $question, $index) {
            if ($index == 0) {
                /** @var HotelAnswer $firstAnswer */
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
