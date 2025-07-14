<?php

namespace App\Models;

/**
 * App\Models\HotelMerchant
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过，待支付保证金，2-已支付保证金，3-审核失败
 * @property string $failure_reason 审核失败原因
 * @property string $company_name 公司名称
 * @property string $region_desc 省市区描述
 * @property string $region_code_list 省市区编码
 * @property string $address_detail 地址详情
 * @property string $business_license_photo 营业执照照片
 * @property string $name 联系人姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $id_card_number 身份证号
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property string $bank_card_owner_name 持卡人姓名
 * @property string $bank_card_number 银行卡号
 * @property string $bank_name 开户银行及支行名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\HotelShopDepositPaymentLog|null $depositInfo
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelMerchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelMerchant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelMerchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelMerchant withoutTrashed()
 * @mixin \Eloquent
 */
class HotelMerchant extends BaseModel
{
    public function depositInfo()
    {
        return $this
            ->hasOne(HotelShopDepositPaymentLog::class, 'merchant_id')
            ->where('status', 1);
    }
}
