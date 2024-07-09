<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class DueDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $startDate = $epic['fields']['customfield_10158'];

        if (!$startDate) {
            return null;
        }

        $this->project = $project;
        $this->assignee = $epic['fields']['customfield_10152']['accountId'] ?? 'unassigned-developer';
        $this->startDate = Carbon::parse($startDate)->format('Y-m-d');
        $this->endDate = Carbon::parse($startDate)->format('Y-m-d');
        $this->description = 'Launch';

        return $this->getDatePeriod();
    }
}
