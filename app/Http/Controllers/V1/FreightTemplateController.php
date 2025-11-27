<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FreightTemplate;
use App\Services\FreightTemplateService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\FreightTemplateInput;
use App\Utils\Inputs\PageInput;

class FreightTemplateController extends Controller
{
    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $list = FreightTemplateService::getInstance()
            ->getPageByShopId($shopId, $input, ['id', 'name', 'title', 'created_at', 'updated_at']);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $detail = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        $detail->area_list = json_decode($detail->area_list);
        return $this->success($detail);
    }

    public function add()
    {
        /** @var FreightTemplateInput $input */
        $input = FreightTemplateInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $freightTemplate = FreightTemplate::new();
        $freightTemplate->shop_id = $shopId;

        FreightTemplateService::getInstance()->update($freightTemplate, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var FreightTemplateInput $input */
        $input = FreightTemplateInput::new();

        $freightTemplate = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        if (is_null($freightTemplate)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前运费模板不存在');
        }

        FreightTemplateService::getInstance()->update($freightTemplate, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $freightTemplate = FreightTemplateService::getInstance()->getFreightTemplateById($id);
        if (is_null($freightTemplate)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前运费模板不存在');
        }
        $freightTemplate->delete();
        return $this->success();
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $list = FreightTemplateService::getInstance()->getListByShopId($shopId, ['id', 'name']);
        return $this->success($list);
    }
}
