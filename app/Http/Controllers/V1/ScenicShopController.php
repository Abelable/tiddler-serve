<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicShopInput;

class ScenicShopController extends Controller
{
    protected $except = ['shopInfo'];

    public function shopInfo()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'type', 'bg', 'logo', 'name'];

        $shop = ScenicShopService::getInstance()->getShopById($id, $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        return $this->success($shop);
    }

    public function updateShopInfo()
    {
        /** @var ScenicShopInput $input */
        $input = ScenicShopInput::new();

        $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId());
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您非商家，暂无店铺');
        }

        ScenicShopService::getInstance()->updateShopInfo($shop, $input);

        return $this->success();
    }
}
