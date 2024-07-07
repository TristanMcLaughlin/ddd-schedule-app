<?php

namespace App\Domain\Entities;

readonly class BacklogTicket
{
    public function __construct(
        private readonly string $ticketId,
        private readonly string $assigneeId,
        private readonly string $priority,
        private readonly string $summary,
        private readonly string $startDate,
        private readonly string $endDate,
    ) {}

    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getAssigneeId(): string
    {
        return $this->assigneeId;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }
}
