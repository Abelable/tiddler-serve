<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\TopMediaService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;

class TopMediaController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = TopMediaService::getInstance()->getTopMediaPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $media = TopMediaService::getInstance()->getTopMediaById($id);
        if (is_null($media)) {
            return $this->fail(CodeResponse::NOT_FOUND, '最佳游记不存在');
        }
        return $this->success($media);
    }

    public function add()
    {
        $mediaType = $this->verifyRequiredInteger('mediaType');
        $mediaId = $this->verifyRequiredInteger('mediaId');
        $cover = $this->verifyString('cover');
        $title = $this->verifyRequiredString('title');

        TopMediaService::getInstance()->createTopMedia($mediaType, $mediaId, $cover, $title);

        Cache::forget('top_media_cache');

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $mediaType = $this->verifyRequiredInteger('mediaType');
        $mediaId = $this->verifyRequiredInteger('mediaId');
        $cover = $this->verifyString('cover');
        $title = $this->verifyRequiredString('title');

        $topMedia = TopMediaService::getInstance()->getTopMediaById($id);
        if (is_null($topMedia)) {
            return $this->fail(CodeResponse::NOT_FOUND, '最佳游记不存在');
        }

        TopMediaService::getInstance()->updateTopMedia($topMedia, $mediaType, $mediaId, $cover, $title);

        Cache::forget('top_media_cache');

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $media = TopMediaService::getInstance()->getTopMediaById($id);
        if (is_null($media)) {
            return $this->fail(CodeResponse::NOT_FOUND, '最佳游记不存在');
        }

        Cache::forget('top_media_cache');

        $media->delete();

        return $this->success();
    }
}
