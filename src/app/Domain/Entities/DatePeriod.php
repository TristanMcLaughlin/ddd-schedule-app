<?php

namespace App\Domain\Entities;

use Illuminate\Contracts\Support\Arrayable;

readonly class DatePeriod implements Arrayable
{
    public function __construct(
        private string $id,
        private string $projectId,
        private string $assigneeId,
        private string $startDate,
        private string $endDate,
        private bool $importedFromJira,
        private ?string $description,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getAssigneeId(): string
    {
        return $this->assigneeId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function isImportedFromJira(): bool
    {
        return $this->importedFromJira;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'project_id' => $this->getProjectId(),
            'assignee_id' => $this->getAssigneeId(),
            'start_date' => $this->getStartDate(),
            'end_date' => $this->getEndDate(),
            'imported_from_jira' => $this->isImportedFromJira(),
            'description' => $this->getDescription(),
        ];
    }
}
