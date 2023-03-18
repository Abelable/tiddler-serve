<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\FanService;
use App\Utils\CodeResponse;

class FanController extends Controller
{
    public function follow()
    {
        $authorId = $this->verifyRequiredId('authorId');
        if ($authorId == $this->userId()) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '不能关注自己哦');
        }

        FanService::getInstance()->newFan($authorId, $this->userId());
        return $this->success();
    }

    public function followStatus()
    {
        $authorId = $this->verifyRequiredId('authorId');

        if ($authorId == $this->userId()) {
            $isFollow = true;
        } else {
            $fanIds = FanService::getInstance()->fanIds($authorId);
            $isFollow = in_array($this->userId(), $fanIds);
        }

        return $this->success([
            'isFollow' => $isFollow
        ]);
    }
}