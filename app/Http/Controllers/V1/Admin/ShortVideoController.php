<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MediaPageInput;

class ShortVideoController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MediaPageInput $input */
        $input = MediaPageInput::new();
        $page = ShortVideoService::getInstance()->adminPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前短视频不存在');
        }
        return $this->success($shortVideo);
    }

    public function add()
    {
        $code = $this->verifyRequiredString('code');
        $name = $this->verifyRequiredString('name');

        $shortVideo = ShortVideoService::getInstance()->getVideo($code);
        if (!is_null($shortVideo)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '当前短视频已存在');
        }

        $shortVideo = ShortVideo::new();
        $shortVideo->code = $code;
        $shortVideo->name = $name;
        $shortVideo->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $name = $this->verifyRequiredString('name');

        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前短视频不存在');
        }

        $shortVideo->name = $name;
        $shortVideo->save();

        return $this->success();
    }

    public function editViews()
    {
        $id = $this->verifyRequiredId('id');
        $views = $this->verifyRequiredInteger('views');

        $shortVideo = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($shortVideo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前短视频不存在');
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前短视频不存在');
        }
        $shortVideo->delete();
        return $this->success();
    }
}
