<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintOption;
use App\Services\ComplaintOptionService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\TypePageInput;

class ComplaintOptionController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var TypePageInput $input */
        $input = TypePageInput::new();
        $list = ComplaintOptionService::getInstance()->getComplaintOptionList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $option = ComplaintOptionService::getInstance()->getComplaintOptionById($id);
        if (is_null($option)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前选项不存在');
        }
        return $this->success($option);
    }

    public function add()
    {
        $type = $this->verifyRequiredInteger('type');
        $title = $this->verifyRequiredString('title');
        $content = $this->verifyString('content');

        $option = ComplaintOption::new();
        $option->type = $type;
        $option->title = $title;
        $option->content = $content;
        $option->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $type = $this->verifyRequiredInteger('type');
        $title = $this->verifyRequiredString('title');
        $content = $this->verifyString('content');

        $option = ComplaintOptionService::getInstance()->getComplaintOptionById($id);
        if (is_null($option)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前选项不存在');
        }

        $option->type = $type;
        $option->title = $title;
        $option->content = $content;
        $option->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $option = ComplaintOptionService::getInstance()->getComplaintOptionById($id);
        if (is_null($option)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前选项不存在');
        }
        $option->delete();
        return $this->success();
    }

    public function options()
    {
        $type = $this->verifyRequiredInteger('type');
        $options = ComplaintOptionService::getInstance()->getComplaintOptions($type, ['id', 'title']);
        return $this->success($options);
    }
}
