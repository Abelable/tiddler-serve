<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelEvaluation;
use App\Services\HotelEvaluationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelEvaluationInput;
use App\Utils\Inputs\PageInput;

class HotelEvaluationController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        $hotelId = $this->verifyRequiredId('hotelId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = HotelEvaluationService::getInstance()->evaluationPage($hotelId, $input);
        $evaluationList = collect($page->items());

        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $evaluationList->map(function (HotelEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->hotel_id);
            return $evaluation;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var HotelEvaluationInput $input */
        $input = HotelEvaluationInput::new();
        HotelEvaluationService::getInstance()->createEvaluation($this->userId(), $input);
        return $this->success();
    }

    public function delete()
    {
        $evaluationId = $this->verifyRequiredId('evaluationId');
        $evaluation = HotelEvaluationService::getInstance()->getUserEvaluation($this->userId(), $evaluationId);
        if (is_null($evaluation)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人评价，无非删除');
        }
        $evaluation->delete();
        return $this->success();
    }
}
