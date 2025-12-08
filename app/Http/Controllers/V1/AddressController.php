<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\Address;
use App\Services\Mall\Goods\AddressService;
use App\Utils\AddressAnalyzeServe;
use App\Utils\CodeResponse;
use App\Utils\Inputs\AddressInput;

class AddressController extends Controller
{
    public function list()
    {
        $columns = ['id', 'is_default', 'name', 'mobile', 'region_desc', 'address_detail'];
        $list = AddressService::getInstance()->getList($this->userId(), $columns);
        return $this->success($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'is_default', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
        $address = AddressService::getInstance()->getById($this->userId(), $id, $columns);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        $address->region_code_list = json_decode($address->region_code_list);
        return $this->success($address);
    }

    public function add()
    {
        /** @var AddressInput $input */
        $input = AddressInput::new();

        $address = Address::new();
        $address->user_id = $this->userId();
        AddressService::getInstance()->updateAddress($address, $input);

        return $this->success();
    }

    public function edit()
    {
        /** @var AddressInput $input */
        $input = AddressInput::new();

        $address = AddressService::getInstance()->getById($this->userId(), $input->id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        AddressService::getInstance()->updateAddress($address, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = AddressService::getInstance()->getById($this->userId(), $id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        $address->delete();
        return $this->success();
    }

    public function analyze()
    {
        $text = $this->verifyRequiredString('text');
        $result = AddressAnalyzeServe::new()->analyze($text);
        return $this->success($result);
    }
}
