<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FreightTemplate;
use App\Services\FreightTemplateService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\FreightTemplateAddInput;
use App\Utils\Inputs\FreightTemplateEditInput;

class FreightTemplateController extends Controller
{
    public function list()
    {
        $list = FreightTemplateService::getInstance()->getListByUserId($this->userId(), ['id', 'name']);
        return $this->success($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'mode', 'name', 'title', 'compute_mode', 'free_quota', 'area_list', 'express_list', 'express_template_lists'];
        $detail = FreightTemplateService::getInstance()->getFreightTemplateById($id, $columns);
        $detail->area_list = json_decode($detail->area_list);
        $detail->express_list = json_decode($detail->express_list);
        $detail->express_template_lists = json_decode($detail->express_template_lists);
        return $this->success($detail);
    }

    public function add()
    {
        /** @var FreightTemplateAddInput $input */
        $input = FreightTemplateAddInput::new();

        $freightTemplate = FreightTemplate::new();
        $freightTemplate->user_id = $this->userId();
        $freightTemplate->mode = $input->mode;
        $freightTemplate->name = $input->name;
        $freightTemplate->title = $input->title;
        $freightTemplate->compute_mode = $input->computeMode;
        $freightTemplate->free_quota = $input->freeQuota;
        $freightTemplate->area_list = $input->areaList;
        $freightTemplate->express_list = $input->expressList;
        $freightTemplate->express_template_lists = $input->expressTemplateLists;
        $freightTemplate->save();

        return $this->success();
    }

    public function edit()
    {
        /** @var FreightTemplateEditInput $input */
        $input = FreightTemplateEditInput::new();

        $freightTemplate = FreightTemplateService::getInstance()->getFreightTemplateById($input->id);
        if (is_null($freightTemplate)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前运费模板不存在');
        }

        $freightTemplate->mode = $input->mode;
        $freightTemplate->name = $input->name;
        $freightTemplate->title = $input->title;
        $freightTemplate->compute_mode = $input->computeMode;
        $freightTemplate->free_quota = $input->freeQuota;
        $freightTemplate->area_list = $input->areaList;
        $freightTemplate->express_list = $input->expressList;
        $freightTemplate->express_template_lists = $input->expressTemplateLists;
        $freightTemplate->save();

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
}
