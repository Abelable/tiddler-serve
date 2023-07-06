<?php

namespace App\Models;

/**
 * App\Models\TicketSpec
 *
 * @property int $id
 * @property int $ticket_id 门票id
 * @property int $category_id 门票分类id
 * @property string $price_list 价格列表：分时间段设置价格及对应库存
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec newQuery()
 * @method static \Illuminate\Database\Query\Builder|TicketSpec onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec wherePriceList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketSpec whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TicketSpec withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TicketSpec withoutTrashed()
 * @mixin \Eloquent
 */
class TicketSpec extends BaseModel
{
}
