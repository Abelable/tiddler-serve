<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicAnswerService;
use App\Services\ScenicQuestionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class ScenicQAController extends Controller
{
    protected $except = ['questionList', 'questionDetail', 'answerList'];

    public function questionList()
    {
        $scenicId = $this->verifyRequiredId('scenicId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ScenicQuestionService::getInstance()->questionPage($scenicId, $input);
        return $this->successPaginate($page);
    }

    public function questionDetail()
    {
        $id = $this->verifyRequiredId('id');
        $question = ScenicQuestionService::getInstance()->getQuestionById($id);
        return $this->success($question);
    }

    public function addQuestion()
    {
        $scenicId = $this->verifyRequiredId('scenicId');
        $content = $this->verifyRequiredString('content');
        ScenicQuestionService::getInstance()->createQuestion($this->userId(), $scenicId, $content);
        return $this->success();
    }

    public function deleteQuestion()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $question = ScenicQuestionService::getInstance()->getUserQuestion($this->userId(), $questionId);
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
        $page = ScenicAnswerService::getInstance()->answerPage($questionId, $input);
        return $this->successPaginate($page);
    }

    public function addAnswer()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $content = $this->verifyRequiredString('content');
        ScenicAnswerService::getInstance()->createAnswer($this->userId(), $questionId, $content);
        return $this->success();
    }

    public function deleteAnswer()
    {
        $answerId = $this->verifyRequiredId('answerId');
        $answer = ScenicAnswerService::getInstance()->getUserAnswer($this->userId(), $answerId);
        if (is_null($answer)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人回答，无非删除');
        }
        $answer->delete();
        return $this->success();
    }
}
