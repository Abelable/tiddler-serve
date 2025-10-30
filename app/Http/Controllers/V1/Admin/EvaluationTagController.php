<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationTag;
use App\Services\EvaluationTagService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\TypePageInput;

class EvaluationTagController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var TypePageInput $input */
        $input = TypePageInput::new();
        $list = EvaluationTagService::getInstance()->getEvaluationTagList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $tag = EvaluationTagService::getInstance()->getEvaluationTagById($id);
        if (is_null($tag)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前标签不存在');
        }
        return $this->success($tag);
    }

    public function add()
    {
        $type = $this->verifyRequiredInteger('type');
        $content = $this->verifyString('content');

        $tag = EvaluationTag::new();
        $tag->type = $type;
        $tag->content = $content;
        $tag->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $type = $this->verifyRequiredInteger('type');
        $content = $this->verifyString('content');

        $tag = EvaluationTagService::getInstance()->getEvaluationTagById($id);
        if (is_null($tag)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前标签不存在');
        }

        $tag->type = $type;
        $tag->content = $content;
        $tag->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $tag = EvaluationTagService::getInstance()->getEvaluationTagById($id);
        if (is_null($tag)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前标签不存在');
        }
        $tag->delete();
        return $this->success();
    }
}
