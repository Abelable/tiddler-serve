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
        $videoIds = $this->verifyArray('videoIds');
        $noteIds = $this->verifyArray('noteIds');

        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds);
        foreach ($videoList as $video) {
            LakeCycleMediaService::getInstance()
                ->createLakeCycleMedia(MediaType::VIDEO, $video->id, $video->cover, $video->title);
        }

        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds);
        foreach ($noteList as $note) {
            LakeCycleMediaService::getInstance()->createLakeCycleMedia(
                MediaType::NOTE,
                $note->id,
                json_decode($note->image_list)[0],
                $note->title
            );
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
