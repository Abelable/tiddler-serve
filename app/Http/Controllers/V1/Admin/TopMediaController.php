<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\TopMediaService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

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

    public function add()
    {
        $type = $this->verifyRequiredInteger('type');
        $id = $this->verifyRequiredInteger('id');
        $cover = $this->verifyRequiredString('cover');
        $title = $this->verifyRequiredString('title');

        TopMediaService::getInstance()->createTopMedia($type, $id, $cover, $title);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $media = TopMediaService::getInstance()->getTopMediaById($id);
        if (is_null($media)) {
            return $this->fail(CodeResponse::NOT_FOUND, '最佳游记不存在');
        }

        $media->delete();

        return $this->success();
    }
}
