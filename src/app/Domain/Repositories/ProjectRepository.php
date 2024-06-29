<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Project;

interface ProjectRepository
{
    public function findById(string $id): ?Project;
    public function save(Project $project): void;
}
