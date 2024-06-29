<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Project;
use App\Domain\Repositories\ProjectRepository;
use App\Infrastructure\Models\ProjectModel;

class EloquentProjectRepository implements ProjectRepository
{
    public function findById(string $id): ?Project
    {
        $projectModel = ProjectModel::find($id);
        if (!$projectModel) {
            return null;
        }

        return $projectModel->toDomainEntity();
    }

    public function save(Project $project): void
    {
        $projectModel = ProjectModel::updateOrCreate(
            ['id' => $project->getId()],
            [
                'name' => $project->getName(),
                'description' => $project->getDescription(),
            ]
        );

        // Optionally, if you need to sync date periods or other relations, do it here.
    }
}
