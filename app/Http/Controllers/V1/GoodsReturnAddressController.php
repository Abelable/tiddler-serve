<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GoodsReturnAddress;
use App\Services\GoodsReturnAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsReturnAddressAddInput;
use App\Utils\Inputs\GoodsReturnAddressEditInput;

class GoodsReturnAddressController extends Controller
{
    public function list()
    {
        $columns = ['id', 'consignee_name', 'mobile', 'address_detail'];
        $list = GoodsReturnAddressService::getInstance()->getListByUserId($this->userId(), $columns);
        return $this->success($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'consignee_name', 'mobile', 'address', 'supplement'];
        $detail = GoodsReturnAddressService::getInstance()->getAddressById($id, $columns);
        return $this->success($detail);
    }

    public function add()
    {
        /** @var GoodsReturnAddressAddInput $input */
        $input = GoodsReturnAddressAddInput::new();

        $address = GoodsReturnAddress::new();
        $address->user_id = $this->userId();
        $address->consignee_name = $input->consigneeName;
        $address->mobile = $input->mobile;
        $address->address_detail = $input->addressDetail;
        if (!empty($input->supplement)) {
            $address->supplement = $input->supplement;
        }
        $address->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var GoodsReturnAddressEditInput $input */
        $input = GoodsReturnAddressEditInput::new();

        $address = GoodsReturnAddressService::getInstance()->getAddressById($input->id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }

        $address->consignee_name = $input->consigneeName;
        $address->mobile = $input->mobile;
        $address->address_detail = $input->addressDetail;
        if (!empty($input->supplement)) {
            $address->supplement = $input->supplement;
        }
        $address->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = GoodsReturnAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        $address->delete();
        return $this->success();
    }
}
