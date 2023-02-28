<?php

namespace App\Services\Media\Note;

use App\Models\TourismNote;
use App\Services\BaseService;

class TourismNoteService extends BaseService
{
    public function getListByIds($ids, $columns = ['*'])
    {
        return TourismNote::query()->whereIn('id', $ids)->get($columns);
    }
}
