<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicAnswer;
use App\Models\ScenicQuestion;
use App\Services\ScenicAnswerService;
use App\Services\ScenicQuestionService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class ScenicQAController extends Controller
{
    protected $except = ['questionList', 'questionDetail', 'answerList'];

    public function questionList()
    {
        $scenicId = $this->verifyRequiredId('scenicId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ScenicQuestionService::getInstance()->questionPage($scenicId, $input);
        $list = collect($page->items())->map(function (ScenicQuestion $question) {
            if ($question->answer_num > 0) {
                /** @var ScenicAnswer $firstAnswer */
                $firstAnswer = $question->firstAnswer();
                $userInfo = UserService::getInstance()->getUserById($firstAnswer->user_id, ['id', 'avatar', 'nickname']);
                $firstAnswer['userInfo'] = $userInfo;
                unset($firstAnswer->user_id);
                $question['firstAnswer'] = $firstAnswer;
            }
            return $question;
        });
        return $this->success($this->paginate($page, $list));
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
        $columns = ['id', 'user_id', 'content', 'like_number', 'created_at'];

        $page = ScenicAnswerService::getInstance()->answerPage($questionId, $input, $columns);
        $answerList = collect($page->items());

        $userIds = $answerList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $answerList->map(function (ScenicAnswer $answer) use ($userList) {
            $userInfo = $userList->get($answer->user_id);
            $answer['userInfo'] = $userInfo;
            unset($answer->user_id);
            return $answer;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function addAnswer()
    {
        $questionId = $this->verifyRequiredId('questionId');
        $content = $this->verifyRequiredString('content');

        $userAnswer = ScenicAnswerService::getInstance()->getUserAnswerByQuestionId($this->userId(), $questionId);
        if (!is_null($userAnswer)) {
            return  $this->fail(CodeResponse::INVALID_OPERATION, '您已回答过该问题');
        }

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
