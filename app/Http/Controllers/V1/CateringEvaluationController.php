<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Catering\CateringEvaluation;
use App\Services\Mall\Catering\CateringEvaluationService;
use App\Services\Mall\Catering\MealTicketOrderService;
use App\Services\Mall\Catering\RestaurantService;
use App\Services\Mall\Catering\SetMealOrderService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CateringEvaluationInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class CateringEvaluationController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = CateringEvaluationService::getInstance()->evaluationPage($restaurantId, $input);
        $evaluationList = collect($page->items());

        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $evaluationList->map(function (CateringEvaluation $evaluation) use ($userList) {
            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);
            unset($evaluation->user_id);
            unset($evaluation->restaurant_id);
            return $evaluation;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var CateringEvaluationInput $input */
        $input = CateringEvaluationInput::new();

        DB::transaction(function () use ($input) {
            CateringEvaluationService::getInstance()->createEvaluation($this->userId(), $input);

            $avgScore = CateringEvaluationService::getInstance()->getAverageScore($input->restaurantId);
            RestaurantService::getInstance()->updateRestaurantAvgScore($input->restaurantId, $avgScore);

            if ($input->type == 1) {
                MealTicketOrderService::getInstance()->finish($this->userId(), $input->orderId);
            } else {
                SetMealOrderService::getInstance()->finish($this->userId(), $input->orderId);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $evaluationId = $this->verifyRequiredId('evaluationId');
        $evaluation = CateringEvaluationService::getInstance()->getUserEvaluation($this->userId(), $evaluationId);
        if (is_null($evaluation)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人评价，无非删除');
        }
        $evaluation->delete();
        return $this->success();
    }
}
