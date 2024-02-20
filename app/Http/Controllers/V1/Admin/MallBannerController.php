<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MallBanner;
use App\Services\MallBannerService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\BannerInput;
use App\Utils\Inputs\BannerPageInput;

class MallBannerController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var BannerPageInput $input */
        $input = BannerPageInput::new();
        $list = MallBannerService::getInstance()->getBannerPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $banner = MallBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }
        return $this->success($banner);
    }

    public function add()
    {
        /** @var BannerInput $input */
        $input = BannerInput::new();
        $banner = MallBanner::new();
        MallBannerService::getInstance()->updateBanner($banner, $input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var BannerInput $input */
        $input = BannerInput::new();

        $banner = MallBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        MallBannerService::getInstance()->updateBanner($banner, $input);

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');
        $banner = MallBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        $banner->status = 1;
        $banner->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');
        $banner = MallBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }

        $banner->status = 2;
        $banner->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $banner = MallBannerService::getInstance()->getBannerById($id);
        if (is_null($banner)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前活动banner不存在');
        }
        $banner->delete();
        return $this->success();
    }
}
