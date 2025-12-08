<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelMerchant;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelMerchantInput;
use App\Utils\Inputs\MerchantPageInput;

class HotelMerchantService extends BaseService
{
    public function createMerchant(HotelMerchantInput $input, $userId)
    {
        $merchant = HotelMerchant::new();
        $merchant->user_id = $userId;
       return $this->updateMerchant($merchant, $input);
    }

    public function updateMerchant(HotelMerchant $merchant, HotelMerchantInput $input)
    {
        $merchant->company_name = $input->companyName;
        $merchant->business_license_photo = $input->businessLicensePhoto;
        $merchant->region_desc = $input->regionDesc;
        $merchant->region_code_list = $input->regionCodeList;
        $merchant->address_detail = $input->addressDetail;
        $merchant->name = $input->name;
        $merchant->mobile = $input->mobile;
        $merchant->email = $input->email;
        $merchant->id_card_number = $input->idCardNumber;
        $merchant->id_card_front_photo = $input->idCardFrontPhoto;
        $merchant->id_card_back_photo = $input->idCardBackPhoto;
        $merchant->hold_id_card_photo = $input->holdIdCardPhoto;
        $merchant->bank_card_owner_name = $input->bankCardOwnerName;
        $merchant->bank_card_number = $input->bankCardNumber;
        $merchant->bank_name = $input->bankName;
        $merchant->save();

        return $merchant;
    }

    public function getMerchantList(MerchantPageInput $input, $columns = ['*'])
    {
        $query = HotelMerchant::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getMerchantByUserId($userId, $columns = ['*'])
    {
        return HotelMerchant::query()->where('user_id', $userId)->first($columns);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return HotelMerchant::query()->find($id, $columns);
    }

    public function getMerchantListByIds(array $ids, $columns = ['*'])
    {
        return HotelMerchant::query()->whereIn('id', $ids)->get($columns);
    }

    public function settled(int $merchantId)
    {
        $merchant = $this->getMerchantById($merchantId);
        if (is_null($merchant)) {
            $this->throwBadArgumentValue();
        }
        if ($merchant->status != 1) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '店铺保证金已支付，请勿重复操作');
        }
        $merchant->status = 2;
        $merchant->save();
        return $merchant;
    }
}
