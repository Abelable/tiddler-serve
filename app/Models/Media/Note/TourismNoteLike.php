<?php

namespace App\Models\Media\Note;

use App\Models\BaseModel;

/**
 * App\Models\TourismNoteLike
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteLike onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteLike whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNoteLike withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteLike withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNoteLike extends BaseModel
{
}
