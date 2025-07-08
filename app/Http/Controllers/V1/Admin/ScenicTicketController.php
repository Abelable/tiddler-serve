<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicTicket;
use App\Services\ScenicMerchantService;
use App\Services\ScenicTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ScenicTicketListInput;

class ScenicTicketController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicTicketListInput $input */
        $input = ScenicTicketListInput::new();
        $page = ScenicTicketService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (ScenicTicket $ticket) {
            $ticket['scenicIds'] = $ticket->scenicIds();
            return $ticket;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket['scenicIds'] = $ticket->scenicIds();

        $merchant = ScenicMerchantService::getInstance()->getMerchantById($ticket->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商不存在');
        }

        $ticket['merchant_info'] = $merchant;

        return $this->success($ticket);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->status = 2;
        $ticket->failure_reason = $reason;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前门票不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
