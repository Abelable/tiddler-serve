<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TicketSpec;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ScenicTicketController extends Controller
{
    protected $except = ['categoryOptions'];

    public function categoryOptions()
    {
        $options = ScenicTicketCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function ticketListTotals()
    {
        return $this->success([
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 1),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 3),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 0),
            ScenicTicketService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function userTicketList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $list = ScenicTicketService::getInstance()->getTicketListByStatus($this->userId(), $input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        $scenicIds = TicketScenicService::getInstance()->getListByTicketId($ticket->id)->pluck('scenic_id')->toArray();
        $specList = TicketSpecService::getInstance()->getSpecListByTicketId($ticket->id, ['category_id', 'price_list']);
        $ticket['scenicIds'] = $scenicIds;
        $ticket['specList'] = $specList;

        return $this->success($ticket);
    }

    public function add()
    {
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();

        $shopId = $this->user()->scenicShop->id;
        if ($shopId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是服务商，无法上传景点门票');
        }

        DB::transaction(function () use ($shopId, $input) {
            $ticket = ScenicTicketService::getInstance()->createTicket($this->userId(), $this->user()->scenicProvider->id, $shopId, $input);
            TicketScenicService::getInstance()->createTicketScenicSpots($ticket->id, $input->scenicIds);
            TicketSpecService::getInstance()->createTicketSpecList($ticket->id, $input->specList);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();

        $ticket = ScenicTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        DB::transaction(function () use ($input, $ticket) {
            $ticket = ScenicTicketService::getInstance()->updateTicket($ticket, $input);
            TicketScenicService::getInstance()->updateTicketScenicSpots($ticket->id, $input->scenicIds);
            TicketSpecService::getInstance()->updateTicketSpecList($ticket->id, $input->specList);
        });

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架景点门票，无法上架');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中景点门票，无法下架');
        }
        $ticket->status = 3;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
