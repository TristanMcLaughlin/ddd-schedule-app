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
        ProjectModel::updateOrCreate(
            ['id' => $project->getId()],
            [
                'name' => $project->getName(),
                'build_status' => $project->getBuildStatus(),
                'rag_status' => $project->getRagStatus(),
            ]
        );
    }
}
