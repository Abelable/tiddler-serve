<?php

namespace App\Models;

/**
 * App\Models\HotelEvaluation
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $hotel_id 酒店id
 * @property string $content 评论内容
 * @property string $image_list 评论图片
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelEvaluation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelEvaluation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelEvaluation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelEvaluation withoutTrashed()
 * @mixin \Eloquent
 */
class HotelEvaluation extends BaseModel
{
}
