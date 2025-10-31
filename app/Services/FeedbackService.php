<?php

namespace App\Services;

use App\Models\Feedback;
use App\Utils\Inputs\FeedbackInput;
use App\Utils\Inputs\PageInput;

class FeedbackService extends BaseService
{
    public function createFeedback(Feedback $feedback, FeedbackInput $input)
    {
        $feedback->content = $input->content;
        $feedback->image_list = json_encode($input->imageList);
        if (!is_null($input->mobile)) {
            $feedback->mobile = $input->mobile;
        }
        $feedback->save();
        return $feedback;
    }

    public function getFeedbackPage(PageInput $input, $columns = ['*'])
    {
        return Feedback::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getFeedbackById($id, $columns = ['*'])
    {
        return Feedback::query()->find($id, $columns);
    }
}
