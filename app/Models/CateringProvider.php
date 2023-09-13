<?php

namespace App\Models;

/**
 * App\Models\CateringProvider
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败
 * @property string $failure_reason 审核失败原因
 * @property int $type 商家类型：1-个人，2-企业
 * @property string $company_name 企业名称
 * @property string $region_desc 省市区描述
 * @property string $region_code_list 省市区编码
 * @property string $address_detail 地址详情
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property string $name 经营者姓名
 * @property string $id_card_number 经营者身份证号
 * @property string $hygienic_license_photo 卫生许可证照片
 * @property string $business_license_photo 营业执照照片
 * @property string $mobile 手机号
 * @property string $bank_card_number 银行卡号
 * @property string $bank_card_owner_name 持卡人姓名
 * @property string $bank_name 开户银行及支行名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereHygienicLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProvider whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringProvider withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringProvider withoutTrashed()
 * @mixin \Eloquent
 */
class CateringProvider extends BaseModel
{
}
