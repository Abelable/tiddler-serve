<?php

namespace App\Services;

use App\Models\PromoterQa;
use App\Utils\Inputs\PageInput;

class PromoterQaService extends BaseService
{
    public function createQa($userId, $promoterId, $question)
    {
        $qa = PromoterQa::new();
        $qa->user_id = $userId;
        $qa->promoter_id = $promoterId;
        $qa->question = $question;
        $qa->save();
        return $qa;
    }

    public function getQaPage($promoterId, PageInput $input, $columns = ['*'])
    {
        return PromoterQa::query()
            ->where('promoter_id', $promoterId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getQaById($id, $columns = ['*'])
    {
        return PromoterQa::query()->find($id, $columns);
    }

    public function getUserQa($userId, $id, $columns = ['*'])
    {
        return PromoterQa::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getPromoterQa($promoterId, $id, $columns = ['*'])
    {
        return PromoterQa::query()->where('promoter_id', $promoterId)->find($id, $columns);
    }

    public function getAnswerCount($promoterId) {
        return PromoterQa::query()
            ->where('promoter_id', $promoterId)
            ->where('answer', '!=', '')
            ->count();
    }

    public function getAnswerAverageDuration($promoterId)
    {
        $avg = PromoterQa::query()
            ->where('promoter_id', $promoterId)
            ->whereNotNull('answer_time')
            ->whereNotNull('created_at')
            ->where('answer', '!=', '')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, answer_time)) as avg_duration')
            ->value('avg_duration');

        return $avg ? round($avg / 60, 2) : 0;
    }
}
