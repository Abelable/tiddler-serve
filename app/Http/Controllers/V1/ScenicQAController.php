<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicQuestion;
use App\Services\ScenicAnswerService;
use App\Services\ScenicQuestionService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class ScenicQAController extends Controller
{
    protected $except = ['questionSummary', 'questionList', 'questionDetail', 'answerList'];

    public function questionSummary()
    {
        $scenicId = $this->verifyRequiredId('scenicId');

        $total = ScenicQuestionService::getInstance()->questionTotal($scenicId);

        $questionList = ScenicQuestionService::getInstance()->questionList($scenicId, 3);
        $list = $questionList->map(function (ScenicQuestion $question, $index) {
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

        DB::transaction(function () use ($questionId, $content) {
            ScenicAnswerService::getInstance()->createAnswer($this->userId(), $questionId, $content);

            /** @var ScenicQuestion $question */
            $question = ScenicQuestionService::getInstance()->getQuestionById($questionId);
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
            $answer = ScenicAnswerService::getInstance()->getUserAnswer($this->userId(), $answerId);
            if (is_null($answer)) {
                return $this->fail(CodeResponse::NOT_FOUND, '非本人回答，无非删除');
            }
            $answer->delete();

            /** @var ScenicQuestion $question */
            $question = ScenicQuestionService::getInstance()->getQuestionById($questionId);
            $question->answer_num = max($question->answer_num - 1, 0);
            $question->save();
        });

        return $this->success();
    }
}
