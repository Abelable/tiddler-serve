<?php

namespace App\Services;

use App\Models\MediaHistory;
use App\Utils\Inputs\PageInput;

class MediaHistoryService extends BaseService
{

    public function getHistoryPage($userId, PageInput $input, $columns = ['*'])
    {
        return MediaHistory::query()
            ->where('user_id', $userId)
            ->orderBy('updated_at', $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createHistory($userId, $mediaType, $mediaId)
    {
        return MediaHistory::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'media_type' => $mediaType,
                'media_id' => $mediaId,
            ],
            [
                'updated_at' => now()
            ]
        );
    }

    public function getHistory($userId, $mediaType, $mediaId, $columns = ['*'])
    {
        return MediaHistory::query()
            ->where('user_id', $userId)
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->first($columns);
    }
}
