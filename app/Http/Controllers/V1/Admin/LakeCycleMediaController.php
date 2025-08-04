<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\LakeCycleMediaService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\PageInput;

class LakeCycleMediaController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LakeCycleMediaService::getInstance()->getAdminPage($input);
        return $this->successPaginate($page);
    }

    public function add()
    {
        $mediaType = $this->verifyRequiredInteger('mediaType');
        $mediaIds = $this->verifyArrayNotEmpty('mediaIds');

        if ($mediaType == MediaType::VIDEO) {
            $mediaList = ShortVideoService::getInstance()->getListByIds($mediaIds);
            foreach ($mediaList as $media) {
                LakeCycleMediaService::getInstance()
                    ->createLakeCycleMedia(MediaType::VIDEO, $media->id, $media->cover, $media->title);
            }
        } else {
            $mediaList = TourismNoteService::getInstance()->getListByIds($mediaIds);
            foreach ($mediaList as $media) {
                LakeCycleMediaService::getInstance()->createLakeCycleMedia(
                    MediaType::NOTE,
                    $media->id,
                    json_decode($media->image_list)[0],
                    $media->title
                );
            }
        }

        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $lakeCycleMedia = LakeCycleMediaService::getInstance()->getLakeCycleMedia($id);
        if (is_null($lakeCycleMedia)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前骑行游记不存在');
        }

        $lakeCycleMedia->sort = $sort;
        $lakeCycleMedia->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $lakeCycleMedia = LakeCycleMediaService::getInstance()->getLakeCycleMedia($id);
        if (is_null($lakeCycleMedia)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前骑行游记不存在');
        }
        $lakeCycleMedia->delete();
        return $this->success();
    }
}
