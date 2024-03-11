<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelQuestion;
use App\Services\HotelAnswerService;
use App\Services\HotelQuestionService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class HotelQAController extends Controller
{
    protected $except = ['questionSummary', 'questionList', 'questionDetail', 'answerList'];

    public function questionSummary()
    {
        $hotelId = $this->verifyRequiredId('hotelId');

        $total = HotelQuestionService::getInstance()->questionTotal($hotelId);

        $questionList = HotelQuestionService::getInstance()->questionList($hotelId, 3);
        $list = $questionList->map(function (HotelQuestion $question, $index) {
            if ($index == 0) {
                return [
                    'content' => $question->content,
                    'firstAnswer' => $question->firstAnswer(),
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

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    public function questionList()
    {
        $hotelId = $this->verifyRequiredId('hotelId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = HotelQuestionService::getInstance()->questionPage($hotelId, $input);
        return $this->successPaginate($page);
    }

    public function questionDetail()
    {
        $id = $this->verifyRequiredId('id');
        $question = HotelQuestionService::getInstance()->getQuestionById($id);
        return $this->success($question);
    }

    public function addQuestion()
    {
        $hotelId = $this->verifyRequiredId('hotelId');
        $content = $this->verifyRequiredString('content');
        HotelQuestionService::getInstance()->createQuestion($this->userId(), $hotelId, $content);
        return $this->success();
    }

    public function deleteQuestion()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $question = HotelQuestionService::getInstance()->getUserQuestion($this->userId(), $questionId);
        if (is_null($question)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人提问，无非删除');
        }
        $question->delete();
        return $this->success();
    }

    public function answerList()
    {
        $questionId = $this->verifyRequiredId('questionId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = HotelAnswerService::getInstance()->answerPage($questionId, $input);
        return $this->successPaginate($page);
    }

    public function addAnswer()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $content = $this->verifyRequiredString('content');

        DB::transaction(function () use ($questionId, $content) {
            HotelAnswerService::getInstance()->createAnswer($this->userId(), $questionId, $content);

            /** @var HotelQuestion $question */
            $question = HotelQuestionService::getInstance()->getQuestionById($questionId);
            $question->answer_num = $question->answer_num + 1;
            $question->save();
        });

        return $this->success();
    }

    public function deleteAnswer()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $answerId = $this->verifyRequiredId('answerId');

        DB::transaction(function () use ($answerId, $questionId) {
            $answer = HotelAnswerService::getInstance()->getUserAnswer($this->userId(), $answerId);
            if (is_null($answer)) {
                return $this->fail(CodeResponse::NOT_FOUND, '非本人回答，无非删除');
            }
            $answer->delete();

            /** @var HotelQuestion $question */
            $question = HotelQuestionService::getInstance()->getQuestionById($questionId);
            $question->answer_num = max($question->answer_num - 1, 0);
            $question->save();
        });

        return $this->success();
    }
}
