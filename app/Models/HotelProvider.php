<?php

namespace App\Models;

/**
 * App\Models\HotelProvider
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProvider whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelProvider withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelProvider withoutTrashed()
 * @mixin \Eloquent
 */
class HotelProvider extends BaseModel
{
}
