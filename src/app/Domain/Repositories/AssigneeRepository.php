<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Assignee;
use App\Domain\Entities\Project;

interface AssigneeRepository
{
    public function findById(string $id): ?Assignee;
    public function save(Assignee $assignee): void;
}
