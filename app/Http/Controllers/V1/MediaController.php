<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Models\Media;
use App\Models\ShortVideo;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\Media\Live\LiveRoomService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
use App\Utils\Inputs\PageInput;

class MediaController extends Controller
{
    protected $except = ['getList'];

    public function getList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = MediaService::getInstance()->list($input, ['user_id', 'type', 'media_id', 'viewers_number', 'praise_number']);
        $list = $this->fillList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function getFollowList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $authorIds = FanService::getInstance()->authorIds($this->userId());
        $page = MediaService::getInstance()->followList($authorIds, $input, ['user_id', 'type', 'media_id', 'viewers_number', 'praise_number']);
        $list = $this->fillList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function fillList($page)
    {
        $mediaList = collect($page->items());

        $userIds = $mediaList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['avatar', 'nickname'])->keyBy('id');

        $liveIds = $mediaList->where('type', 1)->pluck('media_id')->toArray();
        $liveList = LiveRoomService::getInstance()->getListByIds($liveIds)->keyBy('id');

        $videoIds = $mediaList->where('type', 2)->pluck('media_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $nodeIds = $mediaList->where('type', 3)->pluck('media_id')->toArray();
        $nodeList = TourismNoteService::getInstance()->getListByIds($nodeIds)->keyBy('id');

        return $mediaList->map(function (Media $media) use ($nodeList, $videoList, $liveList, $userList) {
            $userInfo = $userList->get($media->user_id);
            switch ($media->type) {
                case 1:
                    /** @var LiveRoom $live */
                    $live = $liveList->get($media->media_id);
                    $media['status'] = $live->status;
                    $media['name'] = $live->name;
                    $media['cover'] = $live->cover;
                    $media['play_url'] = $live->play_url;
                    $media['notice_time'] = $live->notice_time;
                    break;

                case 2:
                    /** @var ShortVideo $video */
                    $video = $videoList->get($media->media_id);
                    $media['cover'] = $video->cover;
                    $media['video_url'] = $video->video_url;
                    $media['title'] = $video->title;
                    break;

                case 3:
                    /** @var TourismNote $node */
                    $node = $nodeList->get($media->media_id);
                    $media['image_list'] = json_decode($node->image_list);
                    $media['title'] = $node->title;
                    break;
            }
            $media['user_info'] = $userInfo;
            unset($media->user_id);
            return $media;
        });
    }
}
