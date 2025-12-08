<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\ShopRefundAddress;
use App\Services\Mall\Goods\ShopRefundAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopRefundAddressInput;

class ShopRefundAddressController extends Controller
{
    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'consignee_name', 'mobile', 'address_detail', 'created_at', 'updated_at'];
        $page = ShopRefundAddressService::getInstance()->getPageByShopId($shopId, $input, $columns);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'consignee_name', 'mobile', 'address_detail', 'supplement'];
        $detail = ShopRefundAddressService::getInstance()->getAddressById($id, $columns);
        return $this->success($detail);
    }

    public function add()
    {
        /** @var ShopRefundAddressInput $input */
        $input = ShopRefundAddressInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $address = ShopRefundAddress::new();
        $address->shop_id = $shopId;

        ShopRefundAddressService::getInstance()->update($address, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ShopRefundAddressInput $input */
        $input = ShopRefundAddressInput::new();

        $address = ShopRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }

        ShopRefundAddressService::getInstance()->update($address, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = ShopRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        $address->delete();
        return $this->success();
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'consignee_name', 'mobile', 'address_detail'];
        $list = ShopRefundAddressService::getInstance()->getListByShopId($shopId, $columns);
        return $this->success($list);
    }
}
