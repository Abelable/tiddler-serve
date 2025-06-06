<?php

namespace App\Services\Media\ShortVideo;

use App\Models\MediaCommodity;
use App\Models\ShortVideo;
use App\Services\BaseService;
use App\Services\MediaCommodityService;
use App\Utils\Enums\MediaType;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\Admin\MediaPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\ShortVideoInput;

class ShortVideoService extends BaseService
{
    public function adminPage(MediaPageInput $input, $columns = ['*'])
    {
        $query = ShortVideo::query();

        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }

        if (!empty($input->userId)) {
            $query = $query->where('user_id', $input->userId);
        }

        if (!empty($input->scenicId)) {
            $relatedProductList = MediaCommodityService::getInstance()
                ->getListByProductIds(ProductType::SCENIC, [$input->scenicId]);
            $videoIds = $relatedProductList->filter(function (MediaCommodity $mediaCommodity) {
                return $mediaCommodity->media_type == MediaType::VIDEO;
            })->pluck('media_id')->toArray();
            $query = $query->whereIn('media_id', $videoIds);
        }

        if (!empty($input->hotelId)) {
            $relatedProductList = MediaCommodityService::getInstance()
                ->getListByProductIds(ProductType::HOTEL, [$input->hotelId]);
            $videoIds = $relatedProductList->filter(function (MediaCommodity $mediaCommodity) {
                return $mediaCommodity->media_type == MediaType::VIDEO;
            })->pluck('media_id')->toArray();
            $query = $query->whereIn('media_id', $videoIds);
        }

        if (!empty($input->restaurantId)) {
            $relatedProductList = MediaCommodityService::getInstance()
                ->getListByProductIds(ProductType::RESTAURANT, [$input->restaurantId]);
            $videoIds = $relatedProductList->filter(function (MediaCommodity $mediaCommodity) {
                return $mediaCommodity->media_type == MediaType::VIDEO;
            })->pluck('media_id')->toArray();
            $query = $query->whereIn('media_id', $videoIds);
        }

        if (!empty($input->goodsId)) {
            $relatedProductList = MediaCommodityService::getInstance()
                ->getListByProductIds(ProductType::GOODS, [$input->goodsId]);
            $videoIds = $relatedProductList->filter(function (MediaCommodity $mediaCommodity) {
                return $mediaCommodity->media_type == MediaType::VIDEO;
            })->pluck('media_id')->toArray();
            $query = $query->whereIn('media_id', $videoIds);
        }

        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function pageList(PageInput $input, $authorIds = null, $curVideoId = 0, $columns = ['*'])
    {
        $query = ShortVideo::query()->where('is_private', 0);
        if (!is_null($authorIds)) {
            $query = $query->whereIn('user_id', $authorIds);
        }
        if ($curVideoId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curVideoId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(SearchPageInput $input)
    {
        return ShortVideo::search($input->keywords)
            ->where('is_private', 0)
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function userPageList(PageInput $input, $userId, $curVideoId = 0, $columns = ['*'])
    {
        $query = ShortVideo::query()->where('user_id', $userId);
        if ($curVideoId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curVideoId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return ShortVideo::query()->whereIn('id', $ids)->with('authorInfo')->get($columns);
    }

    public function newVideo($userId, ShortVideoInput $input)
    {
        $video = ShortVideo::new();
        $video->user_id = $userId;
        $video->title = $input->title;
        $video->cover = $input->cover;
        $video->video_url = $input->videoUrl;
        if (!empty($input->address)) {
            $video->longitude = $input->longitude;
            $video->latitude = $input->latitude;
            $video->address = $input->address;
        }
        if (!empty($input->isPrivate)) {
            $video->is_private = $input->isPrivate;
        }
        $video->save();
        return $video;
    }

    public function getVideo($id, $columns = ['*'])
    {
        return ShortVideo::query()->find($id, $columns);
    }

    public function getUserVideo($userId, $id, $columns = ['*'])
    {
        return ShortVideo::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getVideoByTitle($title, $columns = ['*'])
    {
        return ShortVideo::query()->where('title', $title)->first($columns);
    }

    public function getList()
    {
        return ShortVideo::query()->get();
    }
}
