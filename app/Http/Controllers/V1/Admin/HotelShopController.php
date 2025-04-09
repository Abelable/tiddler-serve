<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;

class HotelShopController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ShopPageInput $input */
        $input = ShopPageInput::new();
        $list = HotelShopService::getInstance()->getShopList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shop = HotelShopService::getInstance()->getShopById($id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }
}
