<?php

namespace App\Services;

use App\Models\TicketSpec;
use App\Utils\CodeResponse;

class TicketSpecService extends BaseService
{
    public function createTicketSpecList($ticketId, array $specList)
    {
        foreach ($specList as $spec) {
            $ticketSpec = TicketSpec::new();
            $ticketSpec->ticket_id = $ticketId;
            $ticketSpec->category_id = $spec['categoryId'];
            $ticketSpec->price_list = $spec['priceList'];
            $ticketSpec->save();
        }
    }

    public function updateTicketSpecList($ticketId, array $specList)
    {
        TicketSpec::query()->where('ticket_id', $ticketId)->delete();
        $this->createTicketSpecList($ticketId, $specList);
    }

    public function getSpecListByTicketId($ticketId, $columns = ['*'])
    {
        return TicketSpec::query()->where('ticket_id', $ticketId)->get($columns);
    }

    public function getPriceList($ticketId, $categoryId, $columns = ['*']) {
        $spec = TicketSpec::query()
            ->where('ticket_id', $ticketId)
            ->where('category_id', $categoryId)
            ->first($columns);
        if (is_null($spec)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '景点门票规格不存在');
        }
        return json_decode($spec->price_list);
    }
}
