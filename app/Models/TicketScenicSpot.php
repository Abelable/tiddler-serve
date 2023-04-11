<?php

namespace App\Models;

/**
 * App\Models\TicketScenicSpot
 *
 * @property int $id
 * @property int $ticket_id 门票id
 * @property int $scenic_id 景点id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot newQuery()
 * @method static \Illuminate\Database\Query\Builder|TicketScenicSpot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketScenicSpot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TicketScenicSpot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TicketScenicSpot withoutTrashed()
 * @mixin \Eloquent
 */
class TicketScenicSpot extends BaseModel
{
}
