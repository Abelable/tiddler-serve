<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FreightTemplate;
use App\Services\FreightTemplateService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\FreightTemplateInput;

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
        return $this->success($detail);
    }

    public function add()
    {
        /** @var FreightTemplateInput $input */
        $input = FreightTemplateInput::new();

        $freightTemplate = FreightTemplate::new();
        $freightTemplate->user_id = $this->userId();

        $this->update($freightTemplate, $input);

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

        $this->update($freightTemplate, $input);

        return $this->success();
    }

    private function update($freightTemplate, FreightTemplateInput $input)
    {
        $freightTemplate->mode = $input->mode;
        $freightTemplate->name = $input->name;
        $freightTemplate->title = $input->title;
        $freightTemplate->compute_mode = $input->computeMode;
        $freightTemplate->free_quota = $input->freeQuota;
        $freightTemplate->area_list = $input->areaList;
        $freightTemplate->express_list = $input->expressList;
        $freightTemplate->express_template_lists = $input->expressTemplateLists;
        $freightTemplate->save();

        return $freightTemplate;
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
