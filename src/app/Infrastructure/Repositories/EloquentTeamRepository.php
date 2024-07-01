<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Team;
use App\Domain\Repositories\TeamRepository;
use App\Infrastructure\Models\TeamModel;

class EloquentTeamRepository implements TeamRepository
{
    public function save(Team $team): void
    {
        TeamModel::updateOrCreate(
            ['id' => $team->getId()],
            [
                'name' => $team->getName(),
            ]
        );
    }

    public function all()
    {
        return TeamModel::with('assignees')->get()
            ->map->toDomainEntity();
    }
}
