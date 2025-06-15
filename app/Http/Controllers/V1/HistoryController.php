<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MediaHistoryService;
use App\Services\ProductHistoryService;
use App\Utils\Inputs\PageInput;

class HistoryController extends Controller
{
    public function mediaHistory()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = MediaHistoryService::getInstance()->getHistoryPage($this->userId(), $input);
        return $this->successPaginate($page);
    }

    public function productHistory()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $type = $this->verifyInteger('type');

        $page = ProductHistoryService::getInstance()->getHistoryPage($this->userId(), $type, $input);

        return $this->successPaginate($page);
    }
}
