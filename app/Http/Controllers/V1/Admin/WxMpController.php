<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\WxMp;
use App\Services\WxMpService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\WxMpInput;
use App\Utils\Inputs\NamePageInput;

class WxMpController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var NamePageInput $input */
        $input = NamePageInput::new();
        $page = WxMpService::getInstance()->getPage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $mp = WxMpService::getInstance()->getById($id);
        if (is_null($mp)) {
            return $this->fail(CodeResponse::NOT_FOUND, '小程序配置不存在');
        }
        return $this->success($mp);
    }

    public function add()
    {
        /** @var WxMpInput $input */
        $input = WxMpInput::new();

        $mp = WxMp::new();
        WxMpService::getInstance()->update($mp, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var WxMpInput $input */
        $input = WxMpInput::new();

        $mp = WxMpService::getInstance()->getById($id);
        if (is_null($mp)) {
            return $this->fail(CodeResponse::NOT_FOUND, '小程序配置不存在');
        }

        WxMpService::getInstance()->update($mp, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $mp = WxMpService::getInstance()->getById($id);
        if (is_null($mp)) {
            return $this->fail(CodeResponse::NOT_FOUND, '小程序配置不存在');
        }

        $mp->delete();

        return $this->success();
    }
}
