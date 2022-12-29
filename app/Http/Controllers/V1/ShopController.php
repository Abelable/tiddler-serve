<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Services\ShopCategoryService;
use App\Utils\Inputs\MerchantSettleInInput;

class ShopController extends Controller
{
    public function categoryOptions()
    {
        $options = ShopCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function addMerchant()
    {
        /** @var MerchantSettleInInput $input */
        $input = MerchantSettleInInput::new();

        $merchant = Merchant::new();
        $merchant->user_id = $this->userId();
        $merchant->type = $input->type;
        if ($input->type === 2) {
            $merchant->company_name = $input->company_name;
            $merchant->business_license_photo = $input->business_license_photo;
        }
        $merchant->region_list = $input->region_list;
        $merchant->address_detail = $input->address_detail;
        $merchant->name = $input->name;
        $merchant->mobile = $input->mobile;
        $merchant->email = $input->email;
        $merchant->id_card_number = $input->id_card_number;
        $merchant->id_card_front_photo = $input->id_card_front_photo;
        $merchant->id_card_back_photo = $input->id_card_back_photo;
        $merchant->hold_id_card_photo = $input->hold_id_card_photo;
        $merchant->bank_card_number = $input->bank_card_number;
        $merchant->bank_name = $input->bank_name;
        $merchant->shop_name = $input->shop_name;
        $merchant->shop_category_id = $input->shop_category_id;
        $merchant->save();

        return $this->success();
    }
}
