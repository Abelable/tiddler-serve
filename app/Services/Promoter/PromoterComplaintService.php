<?php

namespace App\Services\Promoter;

use App\Models\Promoter\PromoterComplaint;
use App\Services\BaseService;
use App\Utils\Inputs\ComplaintInput;
use App\Utils\Inputs\PageInput;

class PromoterComplaintService extends BaseService
{
    public function createComplaint($userId, ComplaintInput $input)
    {
        $complaint = PromoterComplaint::new();
        $complaint->user_id = $userId;
        $complaint->promoter_id = $input->promoterId;
        $complaint->option_ids = $input->optionIds;
        $complaint->content = $input->content ?? '';
        $complaint->imageList = json_encode($input->imageList);
        $complaint->save();
        return $complaint;
    }

    public function getComplaintPage(PageInput $input, $columns = ['*'])
    {
        return PromoterComplaint::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getComplaintById($id, $columns = ['*'])
    {
        return PromoterComplaint::query()->find($id, $columns);
    }
}
