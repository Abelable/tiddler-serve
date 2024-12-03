<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreightTemplate;
use App\Services\FreightTemplateService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\FreightTemplateInput;
use App\Utils\Inputs\PageInput;

class FreightTemplateController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = FreightTemplateService::getInstance()->getSelfList($input);
        $list = collect($page->items())->map(function (FreightTemplate $freightTemplate) {
            $freightTemplate->area_list = json_decode($freightTemplate->area_list);
            return $freightTemplate;
        });
        return $this->success($this->paginate($page, $list));
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
        $freightTemplate = FreightTemplate::new();
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
        $options = FreightTemplateService::getInstance()->getSelfOptions(['id', 'name']);
        return $this->success($options);
    }
}
