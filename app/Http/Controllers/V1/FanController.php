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

    public function cancelFollow()
    {
        $authorId = $this->verifyRequiredId('authorId');

        $fan = FanService::getInstance()->fan($authorId, $this->userId());
        if (is_null($fan)) {
            return $this->fail(CodeResponse::NOT_FOUND, '您未关注该主播');
        }

        $fan->delete();

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
