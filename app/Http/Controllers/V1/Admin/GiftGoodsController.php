<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Mall\Goods\GiftGoodsService;
use App\Services\Mall\Goods\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GiftGoodsInput;
use App\Utils\Inputs\GiftGoodsPageInput;
use Illuminate\Support\Facades\Cache;

class GiftGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var GiftGoodsPageInput $input */
        $input = GiftGoodsPageInput::new();
        $list = GiftGoodsService::getInstance()->getPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var GiftGoodsInput $input */
        $input = GiftGoodsInput::new();

        $giftGoodsList = GiftGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($giftGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            GiftGoodsService::getInstance()->create($input, $goods);
        }

        Cache::forget('gift_goods_type_' . $input->typeId);

        return $this->success();
    }

    public function editDuration()
    {
        $id = $this->verifyRequiredId('id');
        $duration = $this->verifyRequiredInteger('duration');
        GiftGoodsService::getInstance()->updateDuration($id, $duration);
        return $this->success();
    }

    public function editSort()
    {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $giftGoods = GiftGoodsService::getInstance()->getGoodsById($id);
        if (is_null($giftGoods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        GiftGoodsService::getInstance()->updateSort($id, $sort);
        Cache::forget('gift_goods_type_' . $giftGoods->type_id);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = GiftGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
