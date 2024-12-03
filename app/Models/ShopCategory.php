<?php

namespace App\Models;

/**
 * App\Models\ShopCategory
 *
 * @property int $id
 * @property string $name 店铺分类名称
 * @property int $deposit 店铺保证金
 * @property string $adapted_merchant_types 适配的商家类型：1-个人，2-企业
 * @property int $sort 排序
 * @property int $visible 状态: 0-隐藏,1-显示
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereAdaptedMerchantTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereVisible($value)
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ShopCategory extends BaseModel
{
}
