<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Mall\Goods\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ShopPageInput;

class ShopController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ShopPageInput $input */
        $input = ShopPageInput::new();
        $page = ShopService::getInstance()->getShopPage($input);
        return $this->successPaginate($page);
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

    public function options()
    {
        $options = ShopService::getInstance()->getOptions(['id', 'logo', 'name']);
        return $this->success($options);
    }
}
