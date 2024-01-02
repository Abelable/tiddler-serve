<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopListInput;

class ShopController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ShopListInput $input */
        $input = ShopListInput::new();
        $list = ShopService::getInstance()->getShopList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $shop = ShopService::getInstance()->getShopById($id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }
}
