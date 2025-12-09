<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\ShopCategory;
use App\Services\Mall\Goods\ExpressService;
use App\Services\Mall\Goods\ShopCategoryService;
use App\Services\Mall\Goods\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ShopInput;

class ShopController extends Controller
{
    protected $except = ['categoryOptions', 'shopInfo'];

    public function categoryOptions()
    {
        $options = ShopCategoryService::getInstance()
            ->getCategoryOptions(['id', 'name', 'deposit', 'adapted_merchant_types']);
        $options = $options->map(function (ShopCategory $category) {
            $category->adapted_merchant_types = json_decode($category->adapted_merchant_types);
            return $category;
        });
        return $this->success($options);
    }

    public function shopInfo()
    {
        $id = $this->verifyRequiredId('id');

        $shop = ShopService::getInstance()->getShopById($id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        $shop->category_ids = json_decode($shop->category_ids);

        return $this->success($shop);
    }

    public function updateShopInfo()
    {
        /** @var ShopInput $input */
        $input = ShopInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shop = ShopService::getInstance()->getShopById($shopId);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }

        ShopService::getInstance()->updateShopInfo($shop, $input);

        return $this->success();
    }

    public function expressOptions()
    {
        $name = $this->verifyString('name');
        $options = ExpressService::getInstance()->getExpressOptions($name, ['id', 'code', 'name']);
        return $this->success($options);
    }
}
