<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Project;
use App\Domain\Repositories\ProjectRepository;
use App\Infrastructure\Models\ProjectModel;
use Carbon\Carbon;

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
                'build_status' => $project->getBuildStatus(),
                'rag_status' => $project->getRagStatus(),
            ]
        );

        $projectModel->datePeriods()->where('imported_from_jira', true)->delete();
    }

    public function allFutureProjectsWithDatePeriods()
    {
        return ProjectModel::where('build_status', '!=', 'Abandoned')
        ->whereHas('datePeriods', function ($query) {
            $query->where('end_date', '>', Carbon::today());
        })
            ->with('datePeriods')->get()->map->toDomainEntity();
    }
}
