<?php

namespace App\Services;

use App\Models\EvaluationTag;
use App\Utils\Inputs\TypePageInput;

class EvaluationTagService extends BaseService
{
    public function getEvaluationTagList(TypePageInput $input, $columns = ['*'])
    {
        $query = EvaluationTag::query();
        if (!empty($input->scene)) {
            $query = $query->where('scene', $input->scene);
        }
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getEvaluationTagById($id, $columns = ['*'])
    {
        return EvaluationTag::query()->find($id, $columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return EvaluationTag::query()->whereIn('id', $ids)->get($columns);
    }

    public function getEvaluationTagOptions($scene, $type,  $columns = ['*'])
    {
        return EvaluationTag::query()->where('scene', $scene)->where('type', $type)->get($columns);
    }
}
