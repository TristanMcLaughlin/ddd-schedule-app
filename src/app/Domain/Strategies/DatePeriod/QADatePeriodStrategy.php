<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class QADatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $qaDueDate = $epic['fields']['customfield_10098'];
        $pmDueDate = $epic['fields']['customfield_10099'] ?? Carbon::parse($qaDueDate)->addDays(3)->format('Y-m-d');

        if (!$qaDueDate) {
            return null;
        }

        $this->project = $project;
        $this->assignee = '604f1df4311e270068ab9075'; // Hardcoded QA assignee ID
        $this->startDate = Carbon::parse($qaDueDate)->addDay()->format('Y-m-d');
        $this->endDate = Carbon::parse($pmDueDate)->format('Y-m-d');

        return $this->getDatePeriod();
    }
}
