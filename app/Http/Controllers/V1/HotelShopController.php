<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ShopInput;

class HotelShopController extends Controller
{
    protected $except = ['shopInfo'];

    public function shopInfo()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'type', 'bg', 'logo', 'name'];

        $shop = HotelShopService::getInstance()->getShopById($id, $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        return $this->success($shop);
    }

    public function updateShopInfo()
    {
        /** @var ShopInput $input */
        $input = ShopInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shop = HotelShopService::getInstance()->getUserShopByShopId($this->userId(), $shopId);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您非商家，暂无店铺');
        }

        HotelShopService::getInstance()->updateShop($shop, $input);

        return $this->success();
    }
}
