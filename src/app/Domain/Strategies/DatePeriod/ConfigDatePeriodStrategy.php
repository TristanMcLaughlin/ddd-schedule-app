<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class ConfigDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $startDate = $epic['fields']['customfield_10015'];

        $this->project = $project;
        $this->assignee = '63fca0987655a3223a217054'; // Hardcoded config assignee ID
        $this->startDate = Carbon::parse($startDate)->addDay()->format('Y-m-d');
        $this->endDate = Carbon::parse($this->startDate)->format('Y-m-d');

        return $this->getDatePeriod();
    }
}
