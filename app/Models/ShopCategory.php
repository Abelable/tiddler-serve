<?php

namespace App\Models;

/**
 * App\Models\ShopCategory
 *
 * @property int $id
 * @property string $name 店铺分类名称
 * @property int $deposit 店铺保证金
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ShopCategory extends BaseModel
{
}
