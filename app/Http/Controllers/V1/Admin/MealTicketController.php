<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealTicket;
use App\Services\CateringProviderService;
use App\Services\MealTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MealTicketListInput;

class MealTicketController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MealTicketListInput $input */
        $input = MealTicketListInput::new();
        $page = MealTicketService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (MealTicket $ticket) {
            $ticket['restaurantIds'] = $ticket->restaurantIds();
            return $ticket;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket['restaurantIds'] = $ticket->restaurantIds();

        $provider = CateringProviderService::getInstance()->getProviderById($ticket->provider_id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商不存在');
        }

        $ticket['provider_info'] = $provider;

        return $this->success($ticket);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->status = 2;
        $ticket->failure_reason = $reason;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
