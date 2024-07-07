<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\BacklogTicket;
use App\Domain\Repositories\BacklogTicketRepository;
use App\Infrastructure\Models\BacklogTicketModel;
use Illuminate\Support\Collection;

class EloquentBacklogTicketRepository implements BacklogTicketRepository
{
    public function save(BacklogTicket $backlogTicket): void
    {
        BacklogTicketModel::updateOrCreate(
            ['ticket_id' => $backlogTicket->getTicketId()],
            [
                'assignee_id' => $backlogTicket->getAssigneeId(),
                'priority' => $backlogTicket->getPriority(),
                'start_date' => $backlogTicket->getStartDate(),
                'end_date' => $backlogTicket->getEndDate(),
            ]
        );
    }

    public function list(): Collection
    {
        return BacklogTicketModel::all()->map->toDomainEntity();
    }
}
