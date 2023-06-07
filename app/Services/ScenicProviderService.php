<?php

namespace App\Services;

use App\Models\ScenicProvider;
use App\Utils\Inputs\ScenicProviderInput;
use App\Utils\Inputs\ScenicProviderListInput;

class ScenicProviderService extends BaseService
{
    public function createProvider(ScenicProviderInput $input, $userId)
    {
        $provider = ScenicProvider::new();
        $provider->user_id = $userId;
        $provider->company_name = $input->companyName;
        $provider->business_license_photo = $input->businessLicensePhoto;
        $provider->region_desc = $input->regionDesc;
        $provider->region_code_list = $input->regionCodeList;
        $provider->address_detail = $input->addressDetail;
        $provider->name = $input->name;
        $provider->mobile = $input->mobile;
        $provider->email = $input->email;
        $provider->id_card_number = $input->idCardNumber;
        $provider->id_card_front_photo = $input->idCardFrontPhoto;
        $provider->id_card_back_photo = $input->idCardBackPhoto;
        $provider->hold_id_card_photo = $input->holdIdCardPhoto;
        $provider->bank_card_owner_name = $input->bankCardOwnerName;
        $provider->bank_card_number = $input->bankCardNumber;
        $provider->bank_name = $input->bankName;
        $provider->save();

        return $provider;
    }
    public function getProviderList(ScenicProviderListInput $input, $columns = ['*'])
    {
        $query = ScenicProvider::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getProviderByUserId($userId, $columns = ['*'])
    {
        return ScenicProvider::query()->where('user_id', $userId)->first($columns);
    }

    public function getProviderById($id, $columns = ['*'])
    {
        return ScenicProvider::query()->find($id, $columns);
    }

    public function getProviderListByIds(array $ids, $columns = ['*'])
    {
        return ScenicProvider::query()->whereIn('id', $ids)->get($columns);
    }

    public function paySuccess(int $providerId)
    {
        $provider = $this->getProviderById($providerId);
        if (is_null($provider)) {
            $this->throwBadArgumentValue();
        }
        $provider->status = 2;
        $provider->save();
        return $provider;
    }
}
