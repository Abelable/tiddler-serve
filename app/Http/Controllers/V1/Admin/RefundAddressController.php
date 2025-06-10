<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopRefundAddress;
use App\Services\ShopRefundAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ShopRefundAddressInput;
use App\Utils\Inputs\PageInput;

class RefundAddressController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = ShopRefundAddressService::getInstance()->getSelfList($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $address = ShopRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        return $this->success($address);
    }

    public function add()
    {
        /** @var ShopRefundAddressInput $input */
        $input = ShopRefundAddressInput::new();
        $freightTemplate = ShopRefundAddress::new();
        ShopRefundAddressService::getInstance()->update($freightTemplate, $input);

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
        $options = ShopRefundAddressService::getInstance()->getSelfOptions(['id', 'name']);
        return $this->success($options);
    }
}
