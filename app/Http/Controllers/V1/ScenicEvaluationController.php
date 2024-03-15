<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicEvaluation;
use App\Services\ScenicEvaluationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ScenicEvaluationInput;

class ScenicEvaluationController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        $scenicId = $this->verifyRequiredId('scenicId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = ScenicEvaluationService::getInstance()->evaluationPage($scenicId, $input);
        $evaluationList = collect($page->items());

        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $evaluationList->map(function (ScenicEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->scenic_id);
            return $evaluation;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var ScenicEvaluationInput $input */
        $input = ScenicEvaluationInput::new();
        ScenicEvaluationService::getInstance()->createEvaluation($this->userId(), $input);
        return $this->success();
    }

    public function delete()
    {
        $evaluationId = $this->verifyRequiredId('evaluationId');
        $evaluation = ScenicEvaluationService::getInstance()->getUserEvaluation($this->userId(), $evaluationId);
        if (is_null($evaluation)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人评价，无非删除');
        }
        $evaluation->delete();
        return $this->success();
    }
}
