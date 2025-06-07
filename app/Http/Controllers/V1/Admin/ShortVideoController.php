<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoCommentService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\MediaCommodityService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaType;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\Admin\MediaPageInput;
use App\Utils\Inputs\ShortVideoInput;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MediaPageInput $input */
        $input = MediaPageInput::new();

        $page = ShortVideoService::getInstance()->adminPage($input);
        $shortVideoList = collect($page->items());
        $shortVideoIds = $shortVideoList->pluck('id')->toArray();

        $relatedProductList = MediaCommodityService::getInstance()
            ->getListByMediaIds(MediaType::VIDEO, $shortVideoIds)->groupBy('media_id');

        $list = $shortVideoList->map(function (ShortVideo $shortVideo) use ($relatedProductList) {
            $productList = $relatedProductList->get($shortVideo->id);

            $scenicIds = $hotelIds = $restaurantIds = $goodsIds = [];
            foreach ($productList as $mediaCommodity) {
                switch ($mediaCommodity->commodity_type) {
                    case ProductType::SCENIC:
                        $scenicIds[] = $mediaCommodity->commodity_id;
                        break;
                    case ProductType::HOTEL:
                        $hotelIds[] = $mediaCommodity->commodity_id;
                        break;
                    case ProductType::RESTAURANT:
                        $restaurantIds[] = $mediaCommodity->commodity_id;
                        break;
                    case ProductType::GOODS:
                        $goodsIds[] = $mediaCommodity->commodity_id;
                        break;
                }
            }

            $shortVideo['scenicIds'] = $scenicIds;
            $shortVideo['hotelIds'] = $hotelIds;
            $shortVideo['restaurantIds'] = $restaurantIds;
            $shortVideo['goodsIds'] = $goodsIds;

            return $shortVideo;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        $relatedProductList = MediaCommodityService::getInstance()->getListByMediaIds(MediaType::VIDEO, [$id]);
        $scenicIds = $hotelIds = $restaurantIds = $goodsIds = [];
        foreach ($relatedProductList as $mediaCommodity) {
            switch ($mediaCommodity->commodity_type) {
                case ProductType::SCENIC:
                    $scenicIds[] = $mediaCommodity->commodity_id;
                    break;
                case ProductType::HOTEL:
                    $hotelIds[] = $mediaCommodity->commodity_id;
                    break;
                case ProductType::RESTAURANT:
                    $restaurantIds[] = $mediaCommodity->commodity_id;
                    break;
                case ProductType::GOODS:
                    $goodsIds[] = $mediaCommodity->commodity_id;
                    break;
            }
        }
        $shortVideo['scenicIds'] = $scenicIds;
        $shortVideo['hotelIds'] = $hotelIds;
        $shortVideo['restaurantIds'] = $restaurantIds;
        $shortVideo['goodsIds'] = $goodsIds;

        return $this->success($shortVideo);
    }

    public function add()
    {
        $userId = $this->verifyRequiredId('userId');
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () use ($userId, $input) {
            $video = ShortVideoService::getInstance()->createVideo($userId, $input);

            foreach ($input->scenicIds as $scenicId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::SCENIC,
                    $scenicId,
                );
            }

            foreach ($input->hotelIds as $hotelId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::HOTEL,
                    $hotelId,
                );
            }

            foreach ($input->restaurantIds as $restaurantId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::RESTAURANT,
                    $restaurantId,
                );
            }

            foreach ($input->goodsIds as $goodsId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::GOODS,
                    $goodsId,
                );
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $userId = $this->verifyRequiredId('userId');
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        DB::transaction(function () use ($userId, $input, $video) {
            ShortVideoService::getInstance()->updateVideo($video, $userId, $input);

            MediaCommodityService::getInstance()->deleteMediaProduct(MediaType::VIDEO, $video->id);

            foreach ($input->scenicIds as $scenicId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::SCENIC,
                    $scenicId,
                );
            }

            foreach ($input->hotelIds as $hotelId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::HOTEL,
                    $hotelId,
                );
            }

            foreach ($input->restaurantIds as $restaurantId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::RESTAURANT,
                    $restaurantId,
                );
            }

            foreach ($input->goodsIds as $goodsId) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    ProductType::GOODS,
                    $goodsId,
                );
            }
        });

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        $shortVideo->views = $views;
        $shortVideo->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前视频游记不存在');
        }

        DB::transaction(function () use ($shortVideo) {
            $shortVideo->delete();
            MediaCommodityService::getInstance()->deleteMediaProduct(MediaType::VIDEO, $shortVideo->id);
            ShortVideoCollectionService::getInstance()->deleteList($shortVideo->id);
            ShortVideoCommentService::getInstance()->deleteList($shortVideo->id);
            ShortVideoLikeService::getInstance()->deleteList($shortVideo->id);
        });

        return $this->success();
    }
}
