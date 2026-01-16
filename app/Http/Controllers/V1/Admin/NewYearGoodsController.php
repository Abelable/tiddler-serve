<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Mall\Goods\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\NewYearGoodsInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;

class NewYearGoodsController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = NewYearGoodsService::getInstance()->getPage($input);
        return $this->successPaginate($list);
    }

    public function add()
    {
        /** @var NewYearGoodsInput $input */
        $input = NewYearGoodsInput::new();

        $newYearGoodsList = NewYearGoodsService::getInstance()->getFilterGoodsList($input);
        if (count($newYearGoodsList) != 0) {
            return $this->fail(CodeResponse::DATA_EXISTED, '已添加相同商品');
        }

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);

        foreach ($goodsList as $goods) {
            NewYearGoodsService::getInstance()->create($input, $goods);
        }

        Cache::forget('new_year_goods_list');

        return $this->success();
    }

    public function editLuckScore()
    {
        $id = $this->verifyRequiredId('id');
        $luckScore = $this->verifyRequiredInteger('luckScore');

        $newYearGoods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($newYearGoods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        NewYearGoodsService::getInstance()->updateLuckScore($id, $luckScore);
        Cache::forget('new_year_goods_list');

        return $this->success();
    }

    public function editSort()
    {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $newYearGoods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($newYearGoods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        NewYearGoodsService::getInstance()->updateSort($id, $sort);
        Cache::forget('new_year_goods_list');

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $newYearGoods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($newYearGoods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $newYearGoods->status = 1;
        $newYearGoods->save();

        Cache::forget('new_year_goods_list');

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $newYearGoods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($newYearGoods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $newYearGoods->status = 2;
        $newYearGoods->save();

        Cache::forget('new_year_task_list');

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $goods = NewYearGoodsService::getInstance()->getGoodsById($id);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $goods->delete();
        return $this->success();
    }
}
