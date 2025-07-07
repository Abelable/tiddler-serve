<?php

namespace App\Models;

/**
 * App\Models\Merchant
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过，待支付保证金，2-已支付保证金，3-审核失败
 * @property string $failure_reason 审核失败原因
 * @property int $type 商家类型：1-个人，2-企业
 * @property string $company_name 企业名称
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
 * @property-read \App\Models\ShopDepositPaymentLog|null $depositInfo
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|Merchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Merchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Merchant withoutTrashed()
 * @mixin \Eloquent
 */
class Merchant extends BaseModel
{
    public function depositInfo()
    {
        return $this
            ->hasOne(ShopDepositPaymentLog::class, 'merchant_id')
            ->where('status', 1);
    }
}
