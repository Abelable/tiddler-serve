<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\LiveRoomService;
use App\Services\MediaService;
use App\Services\UserService;
use App\Utils\Inputs\PageInput;

class MediaController extends Controller
{
    public function getMediaList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = MediaService::getInstance()->list($input);
        $mediaList = collect($page->items());

        $userIds = $mediaList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getUserListByIds($userIds, ['avatar', 'nickname'])->keyBy('id');

        $liveIds = $mediaList->where('type', 1)->pluck('media_id')->toArray();
        $liveList = LiveRoomService::getInstance()->getListByIds($liveIds, ['status', '']);

    }
}
