<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\ScenicTicketCategory
 *
 * @property int $id
 * @property string $name 景点门票分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicketCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicketCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicTicketCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicketCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicTicketCategory extends BaseModel
{
}
