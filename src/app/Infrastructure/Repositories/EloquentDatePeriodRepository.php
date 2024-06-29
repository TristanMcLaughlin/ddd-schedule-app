<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\DatePeriod;
use App\Domain\Repositories\DatePeriodRepository;
use App\Infrastructure\Models\DatePeriodModel;

class EloquentDatePeriodRepository implements DatePeriodRepository
{
    public function findById(string $id): ?DatePeriod
    {
        $datePeriodModel = DatePeriodModel::find($id);
        if (!$datePeriodModel) {
            return null;
        }

        return $datePeriodModel->toDomainEntity();
    }

    public function save(DatePeriod $datePeriod): void
    {
        DatePeriodModel::updateOrCreate(
            ['id' => $datePeriod->getId()],
            [
                'project_id' => $datePeriod->getProjectId(),
                'assignee_id' => $datePeriod->getAssigneeId(),
                'start_date' => $datePeriod->getStartDate(),
                'end_date' => $datePeriod->getEndDate(),
            ]
        );
    }

    public function deleteByProjectId(string $projectId): void
    {
        DatePeriodModel::where('project_id', $projectId)->delete();
    }
}
