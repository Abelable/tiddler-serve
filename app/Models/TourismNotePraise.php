<?php

namespace App\Models;

/**
 * App\Models\TourismNotePraise
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNotePraise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNotePraise whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNotePraise withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNotePraise withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNotePraise extends BaseModel
{
}
