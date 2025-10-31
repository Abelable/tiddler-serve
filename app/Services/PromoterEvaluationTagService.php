<?php

namespace App\Services;

use App\Models\PromoterEvaluationTag;
use Illuminate\Support\Facades\DB;

class PromoterEvaluationTagService extends BaseService
{
    public function createTag($tagId, $promoterId, $evaluationId)
    {
        $tag = new PromoterEvaluationTag();
        $tag->tag_id = $tagId;
        $tag->promoter_id = $promoterId;
        $tag->evaluation_id = $evaluationId;
        $tag->save();
        return $tag;
    }

    public function getListByEvaluationIds(array $evaluationIds, $columns = ['*'])
    {
        return PromoterEvaluationTag::query()->whereIn('evaluation_id', $evaluationIds)->get($columns);
    }

    public function getPromoterTagList($promoterId)
    {
        return PromoterEvaluationTag::query()
            ->where('promoter_id', $promoterId)
            ->select('tag_id', DB::raw('COUNT(*) as count'))
            ->groupBy('tag_id')
            ->get();
    }

    public function deleteByEvaluationId($evaluationId)
    {
        return PromoterEvaluationTag::query()->where('evaluation_id', $evaluationId)->delete();
    }
}
