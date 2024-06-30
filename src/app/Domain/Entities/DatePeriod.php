<?php

namespace App\Domain\Entities;

readonly class DatePeriod
{
    public function __construct(
        private string $id,
        private string $projectId,
        private string $assigneeId,
        private string $startDate,
        private string $endDate,
        private bool $importedFromJira
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
}
