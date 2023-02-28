<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Services\MediaService;
use App\Services\ShortVideoCollectionService;
use App\Services\ShortVideoGoodsService;
use App\Services\ShortVideoPraiseService;
use App\Services\ShortVideoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaTypeEnums;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShortVideoInput;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $page = ShortVideoService::getInstance()->pageList($id, $input);
        $videoList = collect($page->items());

        $authorIds = $videoList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getUserListByIds($authorIds, ['id', 'name', 'avatar'])->keyBy('id');

        $list = $videoList->map(function (ShortVideo $video) use ($authorList) {
            $authorInfo = $authorList->get($video->user_id);
            $video['author_info'] = $authorInfo;
            unset($video->user_id);
            return $video;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function createVideo()
    {
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () use ($input) {
            $video = ShortVideoService::getInstance()->newVideo($this->userId(), $input);

            if (!empty($input->goodsId)) {
                ShortVideoGoodsService::getInstance()->newGoods($video->id, $input->goodsId);
            }

            MediaService::getInstance()->newMedia($this->userId(), $video->id, MediaTypeEnums::VIDEO);
        });

        return $this->success();
    }

    public function togglePraiseStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $praise = ShortVideoPraiseService::getInstance()->getPraise($this->userId(), $id);
        $praiseNumber = DB::transaction(function () use ($video, $id, $praise) {
            if (!is_null($praise)) {
                $praise->delete();
                $praiseNumber = max($video->praise_number - 1, 0);
            } else {
                ShortVideoPraiseService::getInstance()->newPraise($this->userId(), $id);
                $praiseNumber = $video->praise_number + 1;
            }
            $video->praise_number = $praiseNumber;
            $video->save();

            $media = MediaService::getInstance()->getMedia($id, MediaTypeEnums::VIDEO);
            $media->praise_number = $praiseNumber;
            $media->save();

            return $praiseNumber;
        });

        return $this->success($praiseNumber);
    }

    public function toggleCollectionStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $collection = ShortVideoCollectionService::getInstance()->getCollection($this->userId(), $id);
        $collectionTimes = DB::transaction(function () use ($id, $video, $collection) {
            if (!is_null($collection)) {
                $collection->delete();
                $collectionTimes = max($video->collection_times - 1, 0);
            } else {
                ShortVideoCollectionService::getInstance()->newCollection($this->userId(), $id);
                $collectionTimes = $video->collection_times + 1;
            }
            $video->collection_times = $collectionTimes;
            $video->save();

            return $collectionTimes;
        });

        return $this->success($collectionTimes);
    }

    public function increaseViewersNumber()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        DB::transaction(function () use ($id, $video) {
            $viewersNumber = $video->viewers_number + 1;
            $video->viewers_number = $viewersNumber;
            $video->save();

            $media = MediaService::getInstance()->getMedia($id, MediaTypeEnums::VIDEO);
            $media->viewers_number = $viewersNumber;
            $media->save();
        });

        return $this->success();
    }

    public function share()
    {

    }
}
