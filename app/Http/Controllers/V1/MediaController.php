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
        $liveInput = array_merge((array)$input, [
            'limit' => (string)intval($input->limit / 3)
        ]);
        $liveColumns = ['id', 'status', 'title', 'cover', 'play_url', 'notice_time', 'viewers_number', 'praise_number'];
        $livePage = LiveRoomService::getInstance()->pageList($liveInput, $liveColumns, [1, 3], $authorIds);
        $liveList = collect($livePage->items())->map(function ($live) {
            $live['type'] = MediaTypeEnums::LIVE;
            return $live;
        });

        $videoInput = array_merge((array)$input, [
            'limit' => (string)$liveList->count() < $liveInput['limit'] ? $liveInput['limit'] - $liveList->count() + intval($input->limit / 3) : intval($input->limit / 3)
        ]);
        $videoColumns = ['id', 'cover', 'video_url', 'title', 'praise_number'];
        $videoPage = ShortVideoService::getInstance()->pageList($videoInput, $videoColumns, $authorIds);
        $videoList = collect($videoPage->items())->map(function ($video) {
            $video['type'] = MediaTypeEnums::VIDEO;
            return $video;
        });

        $noteInput = array_merge((array)$input, [
            'limit' => (string)$videoList->count() < $videoInput['limit'] ? $input->limit - $liveInput['limit'] - $videoList->count() : $input->limit - $liveInput['limit'] - $videoInput['limit']
        ]);
        $notePage = TourismNoteService::getInstance()->pageList($noteInput, ['id', 'image_list', 'title', 'praise_number'], $authorIds);
        $noteList = collect($notePage->items())->map(function (TourismNote $note) {
            $note['type'] = MediaTypeEnums::NOTE;
            $note->image_list = json_decode($note->image_list);
            return $note;
        });

        return $noteList->merge($videoList)->merge($liveList)->sortByDesc('created_at');
    }
}
