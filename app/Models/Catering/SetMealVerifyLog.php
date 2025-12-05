<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\SetMealVerifyLog
 *
 * @property int $id
 * @property int $code_id 核销码ID
 * @property int $restaurant_id 核销餐馆id
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereVerifierId($value)
 * @mixin \Eloquent
 */
class SetMealVerifyLog extends BaseModel
{
}
