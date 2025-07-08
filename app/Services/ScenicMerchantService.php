<?php

namespace App\Services;

use App\Models\ScenicMerchant;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicMerchantInput;
use App\Utils\Inputs\ScenicMerchantListInput;

class ScenicMerchantService extends BaseService
{
    public function createMerchant(ScenicMerchantInput $input, $userId)
    {
        $merchant = ScenicMerchant::new();
        $merchant->user_id = $userId;
        return $this->updateMerchant($merchant, $input);
    }

    public function updateMerchant(ScenicMerchant $merchant, ScenicMerchantInput $input)
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

    public function getMerchantList(ScenicMerchantListInput $input, $columns = ['*'])
    {
        $query = ScenicMerchant::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getMerchantByUserId($userId, $columns = ['*'])
    {
        return ScenicMerchant::query()->where('user_id', $userId)->first($columns);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return ScenicMerchant::query()->find($id, $columns);
    }

    public function getMerchantListByIds(array $ids, $columns = ['*'])
    {
        return ScenicMerchant::query()->whereIn('id', $ids)->get($columns);
    }

    public function paySuccess(int $merchantId)
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
