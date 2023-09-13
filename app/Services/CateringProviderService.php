<?php

namespace App\Services;

use App\Models\CateringProvider;
use App\Utils\Inputs\CateringProviderListInput;
use App\Utils\Inputs\CateringProviderInput;

class CateringProviderService extends BaseService
{
    public function createProvider(CateringProviderInput $input, $userId)
    {
        $provider = CateringProvider::new();
        $provider->user_id = $userId;
        $provider->type = $input->type;
        if ($input->type == 2) {
            $provider->company_name = $input->companyName;

        }
        $provider->business_license_photo = $input->businessLicensePhoto;
        $provider->hygienic_license_photo = $input->hygienicLicensePhoto;
        $provider->region_desc = $input->regionDesc;
        $provider->region_code_list = $input->regionCodeList;
        $provider->address_detail = $input->addressDetail;
        $provider->name = $input->name;
        $provider->mobile = $input->mobile;
        $provider->email = $input->email;
        $provider->id_card_number = $input->idCardNumber;
        $provider->id_card_front_photo = $input->idCardFrontPhoto;
        $provider->id_card_back_photo = $input->idCardBackPhoto;
        $provider->save();

        return $provider;
    }

    public function getProviderList(CateringProviderListInput $input, $columns = ['*'])
    {
        $query = CateringProvider::query();
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
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
        return CateringProvider::query()->where('user_id', $userId)->first($columns);
    }

    public function getProviderById($id, $columns = ['*'])
    {
        return CateringProvider::query()->find($id, $columns);
    }

    public function getProviderListByIds(array $ids, $columns = ['*'])
    {
        return CateringProvider::query()->whereIn('id', $ids)->get($columns);
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
