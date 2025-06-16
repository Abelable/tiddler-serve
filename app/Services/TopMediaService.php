<?php

namespace App\Services;

use App\Models\TopMedia;
use App\Utils\Inputs\PageInput;

class TopMediaService extends BaseService
{
    public function createTopMedia($mediaType, $mediaId, $cover, $title)
    {
        $media = TopMedia::new();
        return $this->updateTopMedia($media, $mediaType, $mediaId, $cover, $title);
    }

    public function updateTopMedia(TopMedia $media, $mediaType, $mediaId, $cover, $title)
    {
        $media->media_type = $mediaType;
        $media->media_id = $mediaId;
        $media->cover = $cover ?: '';
        $media->title = $title;
        $media->save();
        return $media;
    }

    public function getTopMediaPage(PageInput $input, $columns = ['*'])
    {
        return TopMedia::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTopMediaById($id, $columns = ['*'])
    {
        return TopMedia::query()->find($id, $columns);
    }
}
