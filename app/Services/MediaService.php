<?php

namespace App\Services;

use App\Models\Media;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class MediaService extends BaseService
{
    public function newMedia($userId, $mediaId, $type)
    {
        $media = Media::new();
        $media->user_id = $userId;
        $media->media_id = $mediaId;
        $media->type = $type;
        $media->save();
        return $media;
    }

    public function list(PageInput $input, $columns = ['*'])
    {
        return Media::query()
            ->orderByRaw("CASE WHEN type = 1 THEN 0 ELSE 1 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function followList($authorIds, PageInput $input, $columns = ['*'])
    {
        return Media::query()
            ->whereIn('user_id', $authorIds)
            ->orderByRaw("CASE WHEN type = 1 THEN 0 ELSE 1 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getMedia($mediaId, $type, $columns = ['*'])
    {
        return Media::query()->where('type', $type)->where('media_id', $mediaId)->first($columns);
    }

    public function deleteMedia($mediaId, $type)
    {
        $media = $this->getMedia($mediaId, $type);
        if (is_null($media)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '当前媒体不存在');
        }
        $media->delete();
    }
}
