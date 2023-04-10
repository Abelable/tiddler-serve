<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Services\ExpressService;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\ScenicShopService;
use App\Services\ShopCategoryService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantSettleInInput;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicShopController extends Controller
{
    protected $except = ['shopInfo'];

    public function shopInfo()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'name', 'avatar', 'cover'];
        $shop = ScenicShopService::getInstance()->getShopById($id, $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'avatar', 'cover'];
        $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }
}
