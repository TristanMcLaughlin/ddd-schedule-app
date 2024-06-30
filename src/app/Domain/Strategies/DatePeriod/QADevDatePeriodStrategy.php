<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class QADevDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $pmDueDate = $epic['fields']['customfield_10099'] ?? Carbon::parse($epic['fields']['customfield_10098'])->addDays(3)->format('Y-m-d');
        $developerId = $epic['fields']['customfield_10152']['accountId'] ?? 'unassigned-developer';

        if (!$pmDueDate) {
            return null;
        }

        $this->project = $project;
        $this->assignee = $developerId;
        $this->startDate = Carbon::parse($pmDueDate)->subDays(3)->format('Y-m-d');
        $this->endDate = Carbon::parse($pmDueDate)->subDay()->format('Y-m-d');

        return $this->getDatePeriod();
    }
}
