<?php

namespace App\Services;

use App\Models\TourismNote;

class TourismNoteService extends BaseService
{
    public function getListByIds($ids, $columns = ['*'])
    {
        return TourismNote::query()->whereIn('id', $ids)->get($columns);
    }
}
