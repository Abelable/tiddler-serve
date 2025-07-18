<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopPickupAddress;
use App\Services\ShopPickupAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ShopPickupAddressInput;

class ShopPickupAddressController extends Controller
{
    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'name', 'longitude', 'latitude', 'address_detail'];
        $list = ShopPickupAddressService::getInstance()->getListByShopId($shopId, $columns);
        return $this->success($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'name', 'open_time_list', 'longitude', 'latitude', 'address_detail'];
        $detail = ShopPickupAddressService::getInstance()->getAddressById($id, $columns);
        $detail->open_time_list = json_decode($detail->open_time_list);

        return $this->success($detail);
    }

    public function add()
    {
        /** @var ShopPickupAddressInput $input */
        $input = ShopPickupAddressInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $address = ShopPickupAddress::new();
        $address->shop_id = $shopId;

        ShopPickupAddressService::getInstance()->update($address, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ShopPickupAddressInput $input */
        $input = ShopPickupAddressInput::new();

        $address = ShopPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }

        ShopPickupAddressService::getInstance()->update($address, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = ShopPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        $address->delete();
        return $this->success();
    }
}
