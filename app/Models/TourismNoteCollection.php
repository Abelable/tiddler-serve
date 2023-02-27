<?php

namespace App\Models;

/**
 * App\Models\TourismNoteCollection
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteCollection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteCollection whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNoteCollection withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteCollection withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNoteCollection extends BaseModel
{
}
