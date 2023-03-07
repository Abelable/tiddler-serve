<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Address
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $is_default 是否为默认地址
 * @property string $name 联系人姓名
 * @property string $mobile 手机号
 * @property string $region_desc 省市区描述
 * @property string $region_code_list 省市区编码
 * @property string $address_detail 地址详情
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Query\Builder|Address onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Address withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Address withoutTrashed()
 * @mixin \Eloquent
 */
	class Address extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $avatar 管理员头像
 * @property string $nickname 管理员昵称
 * @property string $account 管理员账号
 * @property string $password 管理员账号密码
 * @property int $role_id 管理员角色id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Query\Builder|Admin onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Admin withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Admin withoutTrashed()
 * @mixin \Eloquent
 */
	class Admin extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable {}
}

namespace App\Models{
/**
 * App\Models\AdminRole
 *
 * @property int $id
 * @property string $name 管理员角色名称
 * @property string $desc 管理员角色描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdminRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|AdminRole withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdminRole withoutTrashed()
 * @mixin \Eloquent
 */
	class AdminRole extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BaseModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Query\Builder|BaseModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @method static \Illuminate\Database\Query\Builder|BaseModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BaseModel withoutTrashed()
 * @mixin \Eloquent
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cart
 *
 * @property int $id
 * @property int $scene 场景值：1-添加购物车，2-直接购买
 * @property int $status 购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除
 * @property string $status_desc 购物车商品状态描述
 * @property int $user_id 用户id
 * @property int $shop_id 商品所属店铺id
 * @property int $goods_id 商品id
 * @property int $goods_category_id 商品分类id
 * @property string $goods_image 商品图片
 * @property string $goods_name 商品名称
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Query\Builder|Cart onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Cart withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Cart withoutTrashed()
 * @mixin \Eloquent
 */
	class Cart extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Express
 *
 * @property int $id
 * @property string $code 快递编号
 * @property string $name 快递名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Express newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Express newQuery()
 * @method static \Illuminate\Database\Query\Builder|Express onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Express query()
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Express whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Express withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Express withoutTrashed()
 * @mixin \Eloquent
 */
	class Express extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fan
 *
 * @property int $id
 * @property int $author_id 作者id
 * @property int $fan_id 粉丝id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Fan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan newQuery()
 * @method static \Illuminate\Database\Query\Builder|Fan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereFanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Fan withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Fan withoutTrashed()
 * @mixin \Eloquent
 */
	class Fan extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FreightTemplate
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $mode 模板类型：1-自定义模板，2-快递模板
 * @property string $name 模板名称
 * @property string $title 模板标题，可展示在商品详情页
 * @property int $compute_mode 计算方式：1-不计重量和件数，2-按商品件数
 * @property float $free_quota 免费额度
 * @property string $area_list 自定义模板的配送地区列表
 * @property string $express_list 自定义模板的快递方式列表
 * @property string $express_template_lists 快递模板列表
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate newQuery()
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereAreaList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereComputeMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereExpressList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereExpressTemplateLists($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereFreeQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FreightTemplate whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FreightTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	class FreightTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property string $image 列表图片
 * @property string $video 主图视频
 * @property string $image_list 主图图片列表
 * @property string $detail_image_list 详情图片列表
 * @property string $default_spec_image 默认规格图片
 * @property string $name 商品名称
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property int $category_id 商品分类id
 * @property int $return_address_id 退货地址id
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $stock 商品库存
 * @property float $commission_rate 推广佣金比例
 * @property string $spec_list 商品规格列表
 * @property string $sku_list 商品sku
 * @property int $sales_volume 商品销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDefaultSpecImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereReturnAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Goods withoutTrashed()
 * @mixin \Eloquent
 */
	class Goods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GoodsCategory
 *
 * @property int $id
 * @property string $name 店铺分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withoutTrashed()
 * @mixin \Eloquent
 */
	class GoodsCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GoodsReturnAddress
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $consignee_name 收货人姓名
 * @property string $mobile 手机号
 * @property string $address_detail 收获地址
 * @property string $supplement 补充说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereConsigneeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereSupplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress withoutTrashed()
 * @mixin \Eloquent
 */
	class GoodsReturnAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LiveGoods
 *
 * @property int $id
 * @property int $room_id 直播间id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LiveGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveGoods withoutTrashed()
 * @mixin \Eloquent
 */
	class LiveGoods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LiveRoom
 *
 * @property int $id
 * @property int $user_id 直播创建者id
 * @property int $status 直播状态：0-待开播(预告)，1-直播中，2-直播结束, 3-直播预告
 * @property string $title 直播标题
 * @property string $cover 直播封面
 * @property string $share_cover 直播间分享封面
 * @property int $direction 方向：1-竖屏，2-横屏
 * @property string $push_url 推流地址
 * @property string $play_url 拉流地址
 * @property string $playback_url 回放地址
 * @property string $group_id 群聊群组id
 * @property int $viewers_number 观看人数
 * @property int $praise_number 点赞数
 * @property int $notice_time 预告时间
 * @property int $start_time 开播时间
 * @property int $end_time 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $anchorInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Goods[] $goodsList
 * @property-read int|null $goods_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereNoticeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePlayUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePlaybackUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePushUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereShareCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereViewersNumber($value)
 * @method static \Illuminate\Database\Query\Builder|LiveRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveRoom withoutTrashed()
 * @mixin \Eloquent
 */
	class LiveRoom extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Merchant
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败
 * @property int $order_id 商家订单id
 * @property string $failure_reason 审核失败原因
 * @property int $type 商家类型：1-个人，2-企业
 * @property string $company_name 企业名称
 * @property string $region_desc 省市区描述
 * @property string $region_code_list 省市区编码
 * @property string $address_detail 地址详情
 * @property string $business_license_photo 营业执照照片
 * @property string $name 联系人姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $id_card_number 身份证号
 * @property string $id_card_front_photo 身份证正面照片
 * @property string $id_card_back_photo 身份证反面照片
 * @property string $hold_id_card_photo 手持身份证照片
 * @property string $bank_card_owner_name 持卡人姓名
 * @property string $bank_card_number 银行卡号
 * @property string $bank_name 开户银行及支行名称
 * @property string $shop_name 店铺名称
 * @property int $shop_category_id 店铺分类id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|Merchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankCardOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessLicensePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereHoldIdCardPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardBackPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardFrontPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereRegionCodeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereRegionDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereShopCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Merchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Merchant withoutTrashed()
 * @mixin \Eloquent
 */
	class Merchant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MerchantOrder
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $merchant_id 商家id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态：0-待支付，1-支付成功
 * @property string $payment_amount 支付金额
 * @property int $pay_id 支付id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder withoutTrashed()
 * @mixin \Eloquent
 */
	class MerchantOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property string $remarks 订单备注
 * @property int $user_id 用户id
 * @property string $consignee 收件人姓名
 * @property string $mobile 收件人手机号
 * @property string $address 具体收货地址
 * @property int $shop_id 店铺id
 * @property string $shop_avatar 店铺头像
 * @property string $shop_name 店铺名称
 * @property float $goods_price 商品总价格
 * @property float $freight_price 运费
 * @property float $payment_amount 支付金额
 * @property int $pay_id 支付id
 * @property string $pay_time 支付时间
 * @property string $ship_sn 发货编号
 * @property string $ship_channel 快递公司
 * @property string $ship_time 发货时间
 * @property string $confirm_time 用户确认收货时间
 * @property string $finish_time 订单关闭时间
 * @property float $refund_amount 退款金额
 * @property string $refund_type 退款方式
 * @property string $refund_remarks 退款备注
 * @property string $refund_time 退款时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereFreightPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereGoodsPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShipTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderGoods
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $goods_id 商品id
 * @property string $image 列表图片
 * @property string $name 商品名称
 * @property float $price 商品价格
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderGoods withoutTrashed()
 * @mixin \Eloquent
 */
	class OrderGoods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Shop
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $merchant_id 商家id
 * @property string $name 店铺名称
 * @property int $category_id 店铺分类id
 * @property int $type 店铺类型：1-个人，2-企业
 * @property string $cover 店铺封面图片
 * @property string $avatar 店铺头像
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Query\Builder|Shop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Shop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Shop withoutTrashed()
 * @mixin \Eloquent
 */
	class Shop extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShopCategory
 *
 * @property int $id
 * @property string $name 店铺分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopCategory withoutTrashed()
 * @mixin \Eloquent
 */
	class ShopCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShortVideo
 *
 * @property int $id
 * @property int $user_id 作者id
 * @property string $cover 封面
 * @property string $video_url 视频地址
 * @property string $title 视频标题
 * @property int $praise_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $authorInfo
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereVideoUrl($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideo withoutTrashed()
 * @mixin \Eloquent
 */
	class ShortVideo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShortVideoCollection
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection withoutTrashed()
 * @mixin \Eloquent
 */
	class ShortVideoCollection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShortVideoComment
 *
 * @property int $id
 * @property int $video_id 短视频id
 * @property int $comment_id 回复评论id
 * @property int $user_id 用户id
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $userInfo
 */
	class ShortVideoComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShortVideoGoods
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods withoutTrashed()
 * @mixin \Eloquent
 */
	class ShortVideoGoods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShortVideoPraise
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise withoutTrashed()
 * @mixin \Eloquent
 */
	class ShortVideoPraise extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TourismNote
 *
 * @property int $id
 * @property int $user_id 作者id
 * @property string $image_list 主图图片列表
 * @property string $title 标题
 * @property string $content 内容
 * @property int $praise_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $authorInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TourismNoteComment[] $commentList
 * @property-read int|null $comment_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNote withoutTrashed()
 * @mixin \Eloquent
 */
	class TourismNote extends \Eloquent {}
}

namespace App\Models{
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
	class TourismNoteCollection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TourismNoteComment
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $comment_id 回复评论id
 * @property int $user_id 用户id
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $userInfo
 */
	class TourismNoteComment extends \Eloquent {}
}

namespace App\Models{
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
	class TourismNoteGoods extends \Eloquent {}
}

namespace App\Models{
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
	class TourismNotePraise extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $nickname 用户昵称或网络名称
 * @property string $avatar 用户头像图片
 * @property string $mobile 用户手机号码
 * @property string $openid 小程序openid
 * @property string $unionid 微信unionid
 * @property int $gender 性别：0-未知，1-男，2-女
 * @property int $shop_id 店铺id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fan[] $fanList
 * @property-read int|null $fan_list_count
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable {}
}

