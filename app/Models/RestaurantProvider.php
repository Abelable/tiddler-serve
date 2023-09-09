<?php

namespace App\Models;

/**
 * App\Models\RestaurantProvider
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败
 * @property string $failure_reason 审核失败原因
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
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider newQuery()
 * @method static \Illuminate\Database\Query\Builder|RestaurantProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereHygienicLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantProvider whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|RestaurantProvider withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RestaurantProvider withoutTrashed()
 * @mixin \Eloquent
 */
class RestaurantProvider extends BaseModel
{
}
