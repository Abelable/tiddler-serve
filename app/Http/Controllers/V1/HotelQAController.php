<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotelAnswerService;
use App\Services\HotelQuestionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class HotelQAController extends Controller
{
    protected $except = ['questionList', 'questionDetail', 'answerList'];

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
        HotelAnswerService::getInstance()->createAnswer($this->userId(), $questionId, $content);
        return $this->success();
    }

    public function deleteAnswer()
    {
        $answerId = $this->verifyRequiredId('answerId');
        $answer = HotelAnswerService::getInstance()->getUserAnswer($this->userId(), $answerId);
        if (is_null($answer)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人回答，无非删除');
        }
        $answer->delete();
        return $this->success();
    }
}
