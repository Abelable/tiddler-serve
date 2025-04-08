<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopRefundAddress;
use App\Services\ShopRefundAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ShopRefundAddressInput;

class ShopRefundAddressController extends Controller
{
    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'consignee_name', 'mobile', 'address_detail'];
        $list = ShopRefundAddressService::getInstance()->getListByShopId($shopId, $columns);
        return $this->success($list);
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

        $this->update($address, $input);

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

        $this->update($address, $input);

        return $this->success();
    }

    private function update($address, ShopRefundAddressInput $input)
    {
        $address->consignee_name = $input->consigneeName;
        $address->mobile = $input->mobile;
        $address->address_detail = $input->addressDetail;
        if (!empty($input->supplement)) {
            $address->supplement = $input->supplement;
        }
        $address->save();

        return $address;
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
}
