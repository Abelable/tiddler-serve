<?php

namespace App\Services;

use App\Models\ComplaintOption;
use App\Utils\Inputs\TypePageInput;

class ComplaintOptionService extends BaseService
{
    public function getComplaintOptionList(TypePageInput $input, $columns = ['*'])
    {
        $query = ComplaintOption::query();
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getComplaintOptionById($id, $columns = ['*'])
    {
        return ComplaintOption::query()->find($id, $columns);
    }

    public function getComplaintOptions($type, $columns = ['*'])
    {
        return ComplaintOption::query()->where('type', $type)->get($columns);
    }
}
