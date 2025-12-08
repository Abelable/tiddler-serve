<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Promoter\PromoterComplaintService;
use App\Utils\Inputs\ComplaintInput;

class PromoterComplaintController extends Controller
{
    public function submit()
    {
        /** @var ComplaintInput $input */
        $input = ComplaintInput::new();
        PromoterComplaintService::getInstance()->createComplaint($this->userId(), $input);
        return $this->success();
    }
}
