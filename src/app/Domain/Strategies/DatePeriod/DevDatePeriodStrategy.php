<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class DevDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $startDate = $epic['fields']['customfield_10015'];
        $qaDueDate = $epic['fields']['customfield_10098'];

        if (!$startDate) {
            return null;
        }

        $this->project = $project;
        $this->assignee = $epic['fields']['customfield_10152']['accountId'] ?? 'unassigned-developer';
        $this->startDate = Carbon::parse($startDate)->addDay()->format('Y-m-d');
        $this->endDate = Carbon::parse($qaDueDate)->format('Y-m-d');

        return $this->getDatePeriod();
    }
}
