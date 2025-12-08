<?php

namespace App\Services\Theme;

use App\Models\Theme\LakeCycleMedia;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class LakeCycleMediaService extends BaseService
{
    public function createLakeCycleMedia($mediaType, $mediaId, $cover, $title)
    {
        $lakeCycleMedia = LakeCycleMedia::new();
        $lakeCycleMedia->media_type = $mediaType;
        $lakeCycleMedia->media_id = $mediaId;
        $lakeCycleMedia->cover = $cover;
        $lakeCycleMedia->title = $title;
        $lakeCycleMedia->save();
        return $lakeCycleMedia;
    }

    public function getAdminPage(PageInput $input, $columns = ['*'])
    {
        return LakeCycleMedia::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getPage(PageInput $input, $columns = ['*'])
    {
        return LakeCycleMedia::query()
            ->orderBy('sort', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLakeCycleMedia($id, $columns = ['*'])
    {
        return LakeCycleMedia::query()->find($id, $columns);
    }
}
