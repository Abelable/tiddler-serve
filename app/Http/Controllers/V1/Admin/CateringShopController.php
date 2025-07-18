<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;

class CateringShopController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ShopPageInput $input */
        $input = ShopPageInput::new();
        $list = CateringShopService::getInstance()->getShopList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shop = CateringShopService::getInstance()->getShopById($id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }
}
