<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LakeCycleMedia;
use App\Services\HotScenicService;
use App\Services\LakeCycleMediaService;
use App\Services\LakeCycleService;
use App\Services\LakeTripService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\NightTripService;
use App\Services\StarTripService;
use App\Services\UserService;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\PageInput;

class TripTypeController extends Controller
{
    protected $only = [];

    public function hotScenicList()
    {
        $list = HotScenicService::getInstance()->getHotScenicList();
        return $this->success($list);
    }

    public function lakeTripList()
    {
        $lakeId = $this->verifyRequiredId('lakeId');
        $list = LakeTripService::getInstance()->getLakeTripList($lakeId);
        return $this->success($list);
    }

    public function lakeCycleList()
    {
        $routeId = $this->verifyRequiredId('routeId');
        $list = LakeCycleService::getInstance()->getLakeCycleList($routeId);
        return $this->success($list);
    }

    public function lakeCycleMediaList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = LakeCycleMediaService::getInstance()->getPage($input);

        $lakeCycleMediaList = collect($page->items());

        $videoIds = $lakeCycleMediaList->where('media_type', MediaType::VIDEO)->pluck('media_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $lakeCycleMediaList->where('media_type', MediaType::NOTE)->pluck('media_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $videoAuthorIds = $videoList->pluck('user_id');
        $noteAuthorIds = $noteList->pluck('user_id');
        $authorIds = $videoAuthorIds->merge($noteAuthorIds)->unique()->values()->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $lakeCycleMediaList->map(function (LakeCycleMedia $lakeCycleMedia) use ($authorList, $noteList, $videoList) {
            $media = $lakeCycleMedia->media_type == MediaType::VIDEO
                ? $videoList->get($lakeCycleMedia->media_id)
                : $noteList->get($lakeCycleMedia->media_id);

            if ($lakeCycleMedia->media_type == MediaType::NOTE) {
                $media['type'] = MediaType::NOTE;
                $media['imageList'] = json_decode($media->image_list, true);
            } else {
                $media['type'] = MediaType::VIDEO;
            }

            $authorInfo = $authorList->get($media['user_id']);
            $media['authorInfo'] = $authorInfo;
            unset($media['user_id']);

            return $media;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function nightTripList()
    {
        $list = NightTripService::getInstance()->getNightTripList();
        return $this->success($list);
    }

    public function startTripList()
    {
        $list = StarTripService::getInstance()->getStarTripList();
        return $this->success($list);
    }
}
