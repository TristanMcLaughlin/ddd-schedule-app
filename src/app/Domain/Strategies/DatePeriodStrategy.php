<?php

namespace App\Domain\Strategies;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;

interface DatePeriodStrategy
{
    public function createDatePeriod(Project $project, array $epic): ?DatePeriod;
}
