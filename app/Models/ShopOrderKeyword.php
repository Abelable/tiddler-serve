<?php

namespace App\Models;

/**
 * App\Models\ShopOrderKeyword
 *
 * @property int $id
 * @property int $product_type 产品类型
 * @property int $shop_id 店铺id
 * @property string $content 搜索关键字内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopOrderKeyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopOrderKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopOrderKeyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopOrderKeyword withoutTrashed()
 * @mixin \Eloquent
 */
class ShopOrderKeyword extends BaseModel
{
}
