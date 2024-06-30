<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;

abstract class BaseDatePeriodStrategy implements DatePeriodStrategy
{
    protected $assignee;
    protected $startDate;
    protected $endDate;
    protected $importedFromJira = true;

    public function getDatePeriod(): ?DatePeriod
    {
        if (!$this->assignee || !$this->startDate || !$this->endDate) {
            return null;
        }

        return new DatePeriod(
            uniqid(),
            $this->project->getId(),
            $this->assignee,
            $this->startDate,
            $this->endDate,
            $this->importedFromJira
        );
    }

    abstract public function createDatePeriod(Project $project, array $epic): ?DatePeriod;
}
