<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Services\FeedbackService;
use App\Utils\Inputs\Admin\FeedbackInput;

class FeedbackController extends Controller
{
    protected $only = [];

    public function submit()
    {
        /** @var FeedbackInput $input */
        $input = FeedbackInput::new();

        $feedback = Feedback::new();
        $feedback->user_id = $this->userId() ?? 0;
        FeedbackService::getInstance()->createFeedback($feedback, $input);

        return $this->success();
    }
}
