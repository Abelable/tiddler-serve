<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicProject;
use App\Services\ScenicProjectService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class ScenicProjectController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = ScenicProjectService::getInstance()->getProjectList($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $project = ScenicProjectService::getInstance()->getProjectById($id);
        if (is_null($project)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点项目不存在');
        }
        return $this->success($project);
    }

    public function add()
    {
        $name = $this->verifyRequiredString('name');
        $image = $this->verifyRequiredString('image');

        $project = ScenicProject::new();
        $project->name = $name;
        $project->image = $image;
        $project->save();

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyId('id');
        $name = $this->verifyString('name');
        $image = $this->verifyString('image');

        $project = ScenicProjectService::getInstance()->getProjectById($id);
        if (is_null($project)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点项目不存在');
        }

        if (!empty($name)) {
            $project->name = $name;
        }
        if (!empty($image)) {
            $project->name = $image;
        }

        $project->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $project = ScenicProjectService::getInstance()->getProjectById($id);
        if (is_null($project)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点项目不存在');
        }
        $project->delete();
        return $this->success();
    }
}
