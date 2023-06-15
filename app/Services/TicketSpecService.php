<?php

namespace App\Services;

use App\Models\TicketSpec;

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
        $this->updateTicketSpecList($ticketId, $specList);
    }

    public function getSpecListByTicketId($ticketId, $columns = ['*'])
    {
        return TicketSpec::query()->where('ticket_id', $ticketId)->get($columns);
    }
}
