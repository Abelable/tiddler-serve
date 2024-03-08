<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CateringAnswerService;
use App\Services\CateringQuestionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class CateringQAController extends Controller
{
    protected $except = ['questionList', 'questionDetail', 'answerList'];

    public function questionList()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = CateringQuestionService::getInstance()->questionPage($restaurantId, $input);
        return $this->successPaginate($page);
    }

    public function questionDetail()
    {
        $id = $this->verifyRequiredId('id');
        $question = CateringQuestionService::getInstance()->getQuestionById($id);
        return $this->success($question);
    }

    public function addQuestion()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');
        $content = $this->verifyRequiredString('content');
        CateringQuestionService::getInstance()->createQuestion($this->userId(), $restaurantId, $content);
        return $this->success();
    }

    public function deleteQuestion()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $question = CateringQuestionService::getInstance()->getUserQuestion($this->userId(), $questionId);
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
        $page = CateringAnswerService::getInstance()->answerPage($questionId, $input);
        return $this->successPaginate($page);
    }

    public function addAnswer()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $content = $this->verifyRequiredString('content');
        CateringAnswerService::getInstance()->createAnswer($this->userId(), $questionId, $content);
        return $this->success();
    }

    public function deleteAnswer()
    {
        $answerId = $this->verifyRequiredId('answerId');
        $answer = CateringAnswerService::getInstance()->getUserAnswer($this->userId(), $answerId);
        if (is_null($answer)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人回答，无非删除');
        }
        $answer->delete();
        return $this->success();
    }
}
