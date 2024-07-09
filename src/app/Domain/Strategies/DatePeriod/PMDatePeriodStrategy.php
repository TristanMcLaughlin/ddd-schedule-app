<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class PMDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $pmStartDate = $epic['fields']['customfield_10099'];
        $pmDueDate = Carbon::parse($pmStartDate)->addDays(3)->format('Y-m-d');

        if (!$pmStartDate) {
            return null;
        }

        $this->project = $project;
        $this->assignee = $epic['fields']['customfield_10112']['accountId'] ?? 'unassigned-pm';
        $this->startDate = $pmStartDate;
        $this->endDate = $pmDueDate;
        $this->description = 'PM Review';

        return $this->getDatePeriod();
    }
}
