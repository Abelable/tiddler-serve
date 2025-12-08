<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\EvaluationTag;
use App\Models\Promoter\PromoterEvaluation;
use App\Models\Promoter\PromoterEvaluationTag;
use App\Services\EvaluationTagService;
use App\Services\Promoter\PromoterEvaluationService;
use App\Services\Promoter\PromoterEvaluationTagService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\PromoterEvaluationInput;
use Illuminate\Support\Facades\DB;

class PromoterEvaluationController extends Controller
{
    protected $except = ['summary', 'list'];

    public function summary()
    {
        $promoterId = $this->verifyRequiredId('promoterId');

        $promoterTagList = PromoterEvaluationTagService::getInstance()->getPromoterTagList($promoterId);
        $tagIds = $promoterTagList->pluck('tag_id')->toArray();
        $evaluationTagList = EvaluationTagService::getInstance()->getListByIds($tagIds)->keyBy('id');
        $tagList = $promoterTagList->map(function (PromoterEvaluationTag $item) use ($evaluationTagList) {
            return [
                'content' => $evaluationTagList->get($item->tag_id)->content,
                'count' => $item['count'],
            ];
        })->sortByDesc('count')->values();

        $avgScore = PromoterEvaluationService::getInstance()->getAverageScore($promoterId);

        return $this->success([
            'tagList' => $tagList ?? [],
            'avgScore' => $avgScore ?? 0,
        ]);
    }

    public function list()
    {
        $promoterId = $this->verifyRequiredId('promoterId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = PromoterEvaluationService::getInstance()->evaluationPage($promoterId, $input);
        $evaluationList = collect($page->items());
        $evaluationIds = $evaluationList->pluck('id')->toArray();

        $promoterEvaluationTagList = PromoterEvaluationTagService::getInstance()->getListByEvaluationIds($evaluationIds);
        $evaluationTagIds = $promoterEvaluationTagList->pluck('tag_id')->toArray();
        $evaluationTagList = EvaluationTagService::getInstance()->getListByIds($evaluationTagIds)->keyBy('id');
        $tagLists = $promoterEvaluationTagList->map(function (PromoterEvaluationTag $item) use ($evaluationTagList) {
            /** @var EvaluationTag $evaluationTag */
            $evaluationTag = $evaluationTagList->get($item->tag_id);
            $item['content'] = $evaluationTag->content;
            return $item;
        })->groupBy('evaluation_id');

        $userIds = $evaluationList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $evaluationList->map(function (PromoterEvaluation $evaluation) use ($tagLists, $userList) {
            $tagList = $tagLists->get($evaluation->id)->map(function ($item) {
                return $item['content'];
            });
            $evaluation['tagList'] = $tagList;

            $userInfo = $userList->get($evaluation->user_id);
            $evaluation['userInfo'] = $userInfo;
            $evaluation->image_list = json_decode($evaluation->image_list);

            unset($evaluation->user_id);

            return $evaluation;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var PromoterEvaluationInput $input */
        $input = PromoterEvaluationInput::new();

        DB::transaction(function () use ($input) {
            $evaluation = PromoterEvaluationService::getInstance()->createEvaluation($this->userId(), $input);

            foreach ($input->tagIds as $tagId) {
                PromoterEvaluationTagService::getInstance()->createTag($tagId, $input->promoterId, $evaluation->id);
            }
        });

        return $this->success();
    }

    public function delete()
    {
        $evaluationId = $this->verifyRequiredId('evaluationId');

        $evaluation = PromoterEvaluationService::getInstance()->getUserEvaluation($this->userId(), $evaluationId);
        if (is_null($evaluation)) {
            return $this->fail(CodeResponse::NOT_FOUND, '非本人评价，无非删除');
        }

        DB::transaction(function () use ($evaluation) {
            PromoterEvaluationTagService::getInstance()->deleteByEvaluationId($evaluation->id);

            $evaluation->delete();
        });

        return $this->success();
    }
}
