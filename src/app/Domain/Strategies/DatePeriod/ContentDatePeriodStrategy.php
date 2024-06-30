<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use Carbon\Carbon;

class ContentDatePeriodStrategy extends BaseDatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod
    {
        $startDate = $epic['fields']['customfield_10155'];
        $this->project = $project;

        // Sophie if SAM
        $this->assignee = str_contains($epic['fields']['summary'], 'SAM') ? '712020:95961cb3-1293-4a83-bc0c-7ad3cb1291e4' : '712020:f2251f54-8c44-48fb-a37f-080cebf449c6' ;
        $this->startDate = Carbon::parse($startDate)->format('Y-m-d');
        $this->endDate = Carbon::parse($startDate)->format('Y-m-d');

        return $this->getDatePeriod();
    }
}
