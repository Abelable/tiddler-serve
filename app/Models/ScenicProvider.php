<?php

namespace App\Models;

/**
 * App\Models\ScenicProvider
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败
 * @property int $order_id 商家订单id
 * @property string $failure_reason 审核失败原因
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
 * @property string $shop_name 店铺名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProvider whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicProvider withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicProvider withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicProvider extends BaseModel
{
}
