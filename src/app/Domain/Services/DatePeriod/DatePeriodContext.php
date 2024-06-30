<?php

namespace App\Domain\Services\DatePeriod;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;
use App\Domain\Strategies\DatePeriod\DatePeriodStrategy;

class DatePeriodContext
{
    protected $strategies = [];

    public function addStrategy(DatePeriodStrategy $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param Project $project
     * @param array $epic
     * @return DatePeriod[]
     */
    public function createDatePeriods(Project $project, array $epic): array
    {
        $datePeriods = [];

        foreach ($this->strategies as $strategy) {
            $datePeriod = $strategy->createDatePeriod($project, $epic);
            if ($datePeriod) {
                $datePeriods[] = $datePeriod;
            }
        }

        return $datePeriods;
    }
}
