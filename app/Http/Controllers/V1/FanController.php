<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\FanService;

class FanController extends Controller
{
    public function follow()
    {
        $authorId = $this->verifyRequiredId('authorId');
        FanService::getInstance()->newFan($authorId, $this->userId());
        return $this->success();
    }
}
