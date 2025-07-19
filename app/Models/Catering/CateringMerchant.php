<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\CateringMerchant
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过，待支付保证金，2-已支付保证金，3-审核失败
 * @property string $failure_reason 审核失败原因
 * @property int $type 商家类型：1-个体，2-企业
 * @property string $company_name 企业名称
 * @property string $region_desc 省市区描述
 * @property string $region_code_list 省市区编码
 * @property string $address_detail 地址详情
 * @property string $hygienic_license_photo 卫生许可证照片
 * @property string $business_license_photo 营业执照照片
 * @property string $name 经营者姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $id_card_number 经营者身份证号
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property string $bank_card_owner_name 持卡人姓名
 * @property string $bank_card_number 银行卡号
 * @property string $bank_name 开户银行及支行名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Catering\CateringShopDepositPaymentLog|null $depositInfo
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringMerchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereHygienicLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringMerchant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringMerchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringMerchant withoutTrashed()
 * @mixin \Eloquent
 */
class CateringMerchant extends BaseModel
{
    public function depositInfo()
    {
        return $this
            ->hasOne(CateringShopDepositPaymentLog::class, 'merchant_id')
            ->where('status', 1);
    }
}
