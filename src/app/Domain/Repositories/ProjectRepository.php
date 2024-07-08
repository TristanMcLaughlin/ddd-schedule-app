<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Project;
use Carbon\Carbon;

interface ProjectRepository
{
    public function findById(string $id): ?Project;
    public function save(Project $project): void;
    public function projectsWithDatePeriodRange(Carbon $startdate, Carbon $endDate);
}
