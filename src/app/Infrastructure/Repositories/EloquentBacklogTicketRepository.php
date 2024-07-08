<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\BacklogTicket;
use App\Domain\Repositories\BacklogTicketRepository;
use App\Infrastructure\Models\BacklogTicketModel;
use Carbon\Carbon;
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
                'summary' => $backlogTicket->getSummary(),
                'start_date' => $backlogTicket->getStartDate(),
                'end_date' => $backlogTicket->getEndDate(),
                'status' => $backlogTicket->getStatus(),
            ]
        );
    }

    public function list(): Collection
    {
        return BacklogTicketModel::where('end_date', '>=', Carbon::now())->whereNotIn('status', ['Abandoned', 'Done'])->get()->map->toDomainEntity();
    }
}
