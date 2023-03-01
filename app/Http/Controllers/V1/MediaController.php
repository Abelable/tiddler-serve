<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\Media\Live\LiveRoomService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Utils\Enums\MediaTypeEnums;
use App\Utils\Inputs\PageInput;

class MediaController extends Controller
{
    protected $except = ['getList'];

    public function getList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $list = $this->getMediaList($input);

        return $this->success($list);
    }

    public function getFollowList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $authorIds = FanService::getInstance()->authorIds($this->userId());
        $list = $this->getMediaList($input, $authorIds);

        return $this->success($list);
    }

    private function getMediaList(PageInput $input, $authorIds = null)
    {
        /** @var PageInput $liveInput */
        $liveInput = array_merge([
            'limit' => intval($input->limit / 3)
        ], (array)$input);
        $liveColumns = ['id', 'status', 'title', 'cover', 'play_url', 'notice_time'];
        $livePage = LiveRoomService::getInstance()->pageList($liveInput, $liveColumns, [1, 3], $authorIds);
        $liveList = collect($livePage->items())->map(function ($live) {
            $live['type'] = MediaTypeEnums::LIVE;
            return $live;
        });

        /** @var PageInput $videoInput */
        $videoInput = array_merge([
            'limit' => $liveList->count() < $liveInput->limit ? $liveInput->limit - $liveList->count() + intval($input->limit / 3) : intval($input->limit / 3)
        ], (array)$input);
        $videoColumns = ['id', 'cover', 'video_url', 'title', 'praise_number'];
        $videoPage = ShortVideoService::getInstance()->pageList($videoInput, $videoColumns, $authorIds);
        $videoList = collect($videoPage->items())->map(function ($video) {
            $video['type'] = MediaTypeEnums::VIDEO;
            return $video;
        });

        /** @var PageInput $noteInput */
        $noteInput = array_merge([
            'limit' => $videoList->count() < $videoInput->limit ? $input->limit - $liveInput->limit - $videoList->count() : $input->limit - $liveInput->limit - $videoInput->limit
        ], (array)$input);
        $notePage = TourismNoteService::getInstance()->pageList($noteInput, ['id', 'image_list', 'title', 'praise_number'], $authorIds);
        $noteList = collect($notePage->items())->map(function (TourismNote $note) {
            $note['type'] = MediaTypeEnums::NOTE;
            $note->image_list = json_decode($note->image_list);
            return $note;
        });

        return $noteList->merge($videoList)->merge($liveList)->sortByDesc('created_at');
    }
}
