<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\ShopPickupAddress;
use App\Services\Mall\Goods\ShopPickupAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopPickupAddressInput;

class PickupAddressController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ShopPickupAddressService::getInstance()->getSelfList($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $address = ShopPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        $address->open_time_list = json_decode($address->open_time_list, true);

        return $this->success($address);
    }

    public function add()
    {
        /** @var ShopPickupAddressInput $input */
        $input = ShopPickupAddressInput::new();
        $freightTemplate = ShopPickupAddress::new();
        ShopPickupAddressService::getInstance()->update($freightTemplate, $input);

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

    public function options()
    {
        $options = ShopPickupAddressService::getInstance()->getSelfOptions(['id', 'name']);
        return $this->success($options);
    }
}
