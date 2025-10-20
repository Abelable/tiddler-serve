<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringMerchant;
use App\Services\BaseService;
use App\Utils\Inputs\CateringMerchantInput;
use App\Utils\Inputs\MerchantPageInput;

class CateringMerchantService extends BaseService
{
    public function createMerchant(CateringMerchantInput $input, $userId)
    {
        $merchant = CateringMerchant::new();
        $merchant->user_id = $userId;
       return $this->updateMerchant($merchant, $input);
    }

    public function updateMerchant(CateringMerchant $merchant, CateringMerchantInput $input)
    {
        $merchant->type = $input->type;
        if ($input->type == 1) {
            $merchant->company_name = $input->companyName;
        }
        $merchant->region_desc = $input->regionDesc;
        $merchant->region_code_list = $input->regionCodeList;
        $merchant->address_detail = $input->addressDetail;
        $merchant->business_license_photo = $input->businessLicensePhoto;
        $merchant->hygienic_license_photo = $input->hygienicLicensePhoto;
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
        $query = CateringMerchant::query();
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
        return CateringMerchant::query()->where('user_id', $userId)->first($columns);
    }

    public function getMerchantById($id, $columns = ['*'])
    {
        return CateringMerchant::query()->find($id, $columns);
    }

    public function getMerchantListByIds(array $ids, $columns = ['*'])
    {
        return CateringMerchant::query()->whereIn('id', $ids)->get($columns);
    }

    public function paySuccess(int $merchantId)
    {
        $merchant = $this->getMerchantById($merchantId);
        if (is_null($merchant)) {
            $this->throwBadArgumentValue();
        }
        $merchant->status = 2;
        $merchant->save();
        return $merchant;
    }
}
