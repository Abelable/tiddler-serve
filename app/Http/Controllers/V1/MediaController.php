<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Models\ShortVideo;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\Media\Live\LiveRoomService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
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
        $liveColumns = ['id', 'user_id', 'status', 'title', 'cover', 'play_url', 'notice_time', 'viewers_number', 'praise_number'];
        $livePage = LiveRoomService::getInstance()->pageList($liveInput, $liveColumns, [1, 3], $authorIds);
        $liveListCollect = collect($livePage->items());
        $liveAnchorIds = $liveListCollect->pluck('user_id')->toArray();
        $anchorList = UserService::getInstance()->getListByIds($liveAnchorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $liveList = $liveListCollect->map(function (LiveRoom $live) use ($anchorList) {
            $live['type'] = MediaTypeEnums::LIVE;

            $anchorInfo = $anchorList->get($live->user_id);
            $live['anchor_info'] = $anchorInfo;
            unset($live->user_id);

            return $live;
        });

        $videoInput = array_merge((array)$input, [
            'limit' => (string)$liveList->count() < $liveInput['limit'] ? $liveInput['limit'] - $liveList->count() + intval($input->limit / 3) : intval($input->limit / 3)
        ]);
        $videoColumns = ['id', 'user_id', 'cover', 'video_url', 'title', 'praise_number'];
        $videoPage = ShortVideoService::getInstance()->pageList($videoInput, $videoColumns, $authorIds);
        $videoListCollect = collect($videoPage->items());
        $videoAuthorIds = $videoListCollect->pluck('user_id')->toArray();
        $videoAuthorList = UserService::getInstance()->getListByIds($videoAuthorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $videoList = $videoListCollect->map(function (ShortVideo $video) use ($videoAuthorList) {
            $video['type'] = MediaTypeEnums::VIDEO;

            $authorInfo = $videoAuthorList->get($video->user_id);
            $video['author_info'] = $authorInfo;
            unset($video->user_id);

            return $video;
        });

        $noteInput = array_merge((array)$input, [
            'limit' => (string)$videoList->count() < $videoInput['limit'] ? $input->limit - $liveInput['limit'] - $videoList->count() : $input->limit - $liveInput['limit'] - $videoInput['limit']
        ]);
        $notePage = TourismNoteService::getInstance()->pageList($noteInput, ['id', 'user_id', 'image_list', 'title', 'praise_number'], $authorIds);
        $noteListCollect = collect($notePage->items());
        $noteAuthorIds = $noteListCollect->pluck('user_id')->toArray();
        $noteAuthorList = UserService::getInstance()->getListByIds($noteAuthorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $noteList = $noteListCollect->map(function (TourismNote $note) use ($noteAuthorList) {
            $note['type'] = MediaTypeEnums::NOTE;
            $note->image_list = json_decode($note->image_list);

            $authorInfo = $noteAuthorList->get($note->user_id);
            $note['author_info'] = $authorInfo;
            unset($note->user_id);

            return $note;
        });

        return $liveList->merge($noteList)->merge($videoList)->sortByDesc('created_at');
    }
}
