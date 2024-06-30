<?php

namespace App\Domain\Strategies;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;

abstract class BaseDatePeriodStrategy implements DatePeriodStrategy
{
    protected $assignee;
    protected $startDate;
    protected $endDate;

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
            $this->endDate
        );
    }

    abstract public function createDatePeriod(Project $project, array $epic): ?DatePeriod;
}
