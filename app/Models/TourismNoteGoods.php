<?php

namespace App\Models;

/**
 * App\Models\TourismNoteGoods
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNoteGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteGoods withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNoteGoods extends BaseModel
{
}
