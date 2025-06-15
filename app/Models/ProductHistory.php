<?php

namespace App\Models;

/**
 * App\Models\ProductHistory
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $product_type 产品类型
 * @property int $product_id 产品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProductHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductHistory whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ProductHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProductHistory withoutTrashed()
 * @mixin \Eloquent
 */
class ProductHistory extends BaseModel
{
}
