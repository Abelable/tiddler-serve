<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsAddInput;
use App\Utils\Inputs\GoodsEditInput;
use App\Utils\Inputs\StatusPageInput;

class ScenicTicketController extends Controller
{
    protected $except = ['categoryOptions'];

    public function categoryOptions()
    {
        $options = ScenicTicketCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function ticketListTotals()
    {
        return $this->success([
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 1),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 3),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 0),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function userTicketList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $list = ScenicTicketService::getInstance()->getTicketListByStatus($this->userId(), $input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        return $this->success($ticket);
    }

    public function add()
    {
        /** @var GoodsAddInput $input */
        $input = GoodsAddInput::new();

        $goods = Goods::new();
        $shopId = $this->user()->shop->id;
        if ($shopId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是商家，无法上传景点门票');
        }
        $goods->shop_id = $shopId;
        $goods->user_id = $this->userId();
        $goods->image = $input->image;
        if (!empty($input->video)) {
            $goods->video = $input->video;
        }
        $goods->image_list = $input->imageList;
        $goods->detail_image_list = $input->detailImageList;
        $goods->default_spec_image = $input->defaultSpecImage;
        $goods->name = $input->name;
        $goods->freight_template_id = $input->freightTemplateId;
        $goods->category_id = $input->categoryId;
        $goods->return_address_id = $input->returnAddressId;
        $goods->price = $input->price;
        if (!empty($input->marketPrice)) {
            $goods->market_price = $input->marketPrice;
        }
        $goods->stock = $input->stock;
        $goods->sales_commission_rate = $input->salesCommissionRate;
        $goods->promotion_commission_rate = $input->promotionCommissionRate;
        $goods->spec_list = $input->specList;
        $goods->sku_list = $input->skuList;
        $goods->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var GoodsEditInput $input */
        $input = GoodsEditInput::new();

        $ticket = ScenicTicketService::getInstance()->getTicketById($input->id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家景点门票，无法编辑');
        }
        if ($ticket->status != 2) {
            return $this->fail(CodeResponse::FORBIDDEN, '非审核未通过景点门票，无法编辑');
        }

        $ticket->status = 0;
        $ticket->failure_reason = '';
        $ticket->image = $input->image;
        if (!empty($input->video)) {
            $ticket->video = $input->video;
        }
        $ticket->image_list = $input->imageList;
        $ticket->detail_image_list = $input->detailImageList;
        $ticket->default_spec_image = $input->defaultSpecImage;
        $ticket->name = $input->name;
        $ticket->freight_template_id = $input->freightTemplateId;
        $ticket->category_id = $input->categoryId;
        $ticket->return_address_id = $input->returnAddressId;
        $ticket->price = $input->price;
        if (!empty($input->marketPrice)) {
            $ticket->market_price = $input->marketPrice;
        }
        $ticket->stock = $input->stock;
        $ticket->sales_commission_rate = $input->salesCommissionRate;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate;
        $ticket->spec_list = $input->specList;
        $ticket->sku_list = $input->skuList;
        $ticket->save();

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家景点门票，无法上架该景点门票');
        }
        if ($ticket->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架景点门票，无法上架');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家景点门票，无法下架该景点门票');
        }
        if ($ticket->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中景点门票，无法下架');
        }
        $ticket->status = 3;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->shop_id != $this->user()->shop_id) {
            return $this->fail(CodeResponse::FORBIDDEN, '非当前商家景点门票，无法删除');
        }
        $ticket->delete();

        return $this->success();
    }
}
