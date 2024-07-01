<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Assignee;
use App\Domain\Repositories\AssigneeRepository;
use App\Infrastructure\Models\AssigneeModel;

class EloquentAssigneeRepository implements AssigneeRepository
{
    public function findById(string $id): ?Assignee
    {
        $assigneeModel = AssigneeModel::find($id);
        if (!$assigneeModel) {
            return null;
        }

        return $assigneeModel->toDomainEntity();
    }

    public function save(Assignee $assignee): void
    {
        AssigneeModel::updateOrCreate(
            ['id' => $assignee->getId()],
            [
                'name' => $assignee->getName(),
                'role' => $assignee->getRole(),
                'team_id' => $assignee->getTeamId(),
            ]
        );
    }

    public function all()
    {
        return AssigneeModel::all()->map->toDomainEntity();
    }
}
