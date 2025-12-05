<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\MealTicketVerifyLog
 *
 * @property int $id
 * @property int $code_id 核销码ID
 * @property int $restaurant_id 核销餐馆id
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereVerifierId($value)
 * @mixin \Eloquent
 */
class MealTicketVerifyLog extends BaseModel
{
}
