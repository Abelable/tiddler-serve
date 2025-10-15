<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Utils\Inputs\TaskPageInput;

class TaskController extends Controller
{
    protected $only = [];

    public function list()
    {
        /** @var TaskPageInput $input */
        $input = TaskPageInput::new();
        $list = TaskService::getInstance()->getTaskPage($input);
        return $this->successPaginate($list);
    }
}
