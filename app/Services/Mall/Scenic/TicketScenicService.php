<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\TicketScenicSpot;
use App\Services\BaseService;

class TicketScenicService extends BaseService
{
    public function createTicketScenicSpots($ticketId, array $scenicIds)
    {
        foreach ($scenicIds as $scenicId) {
            $scenic = TicketScenicSpot::new();
            $scenic->ticket_id = $ticketId;
            $scenic->scenic_id = $scenicId;
            $scenic->save();
        }
    }

    public function updateTicketScenicSpots($ticketId, array $scenicIds)
    {
        TicketScenicSpot::query()->where('ticket_id', $ticketId)->delete();
        $this->createTicketScenicSpots($ticketId, $scenicIds);
    }

    public function getListByTicketId($ticketId, $columns = ['*'])
    {
        return TicketScenicSpot::query()->where('ticket_id', $ticketId)->get($columns);
    }

    public function getListByTicketIds(array $ticketIds, $columns = ['*'])
    {
        return TicketScenicSpot::query()->whereIn('ticket_id', $ticketIds)->get($columns);
    }

    public function getListByScenicId($scenicId, $columns = ['*'])
    {
        return TicketScenicSpot::query()->where('scenic_id', $scenicId)->get($columns);
    }
}
