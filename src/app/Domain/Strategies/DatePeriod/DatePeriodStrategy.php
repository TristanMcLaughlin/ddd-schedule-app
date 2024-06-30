<?php

namespace App\Domain\Strategies\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;

interface DatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod;
}
