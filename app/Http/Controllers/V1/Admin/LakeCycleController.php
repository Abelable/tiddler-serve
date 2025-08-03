<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\LakeCycleService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\LakeCycleInput;
use App\Utils\Inputs\PageInput;

class LakeCycleController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = LakeCycleService::getInstance()->getLakeCyclePage($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $lakeCycle = LakeCycleService::getInstance()->getLakeCycle($id);
        if (is_null($lakeCycle)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }
        return $this->success($lakeCycle);
    }

    public function add()
    {
        /** @var LakeCycleInput $input */
        $input = LakeCycleInput::new();

        LakeCycleService::getInstance()->createLakeCycle($input);

        return $this->success();
    }

    public function edit()
    {
        /** @var LakeCycleInput $input */
        $input = LakeCycleInput::new();
        $id = $this->verifyRequiredId('id');

        $lakeCycle = LakeCycleService::getInstance()->getLakeCycle($id);
        if (is_null($lakeCycle)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }

        LakeCycleService::getInstance()->updateLakeCycle($lakeCycle, $input);
        return $this->success();
    }

    public function editSort() {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $lakeCycle = LakeCycleService::getInstance()->getLakeCycle($id);
        if (is_null($lakeCycle)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }

        $lakeCycle->sort = $sort;
        $lakeCycle->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $lakeCycle = LakeCycleService::getInstance()->getLakeCycle($id);
        if (is_null($lakeCycle)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前岛屿不存在');
        }
        $lakeCycle->delete();
        return $this->success();
    }
}
