<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\DatePeriod;
use App\Domain\Entities\Project;

interface DatePeriodRepository
{
    public function findById(string $id): ?DatePeriod;
    public function save(DatePeriod $datePeriod): void;
}
