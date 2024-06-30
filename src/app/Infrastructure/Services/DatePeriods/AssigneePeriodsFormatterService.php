<?php

namespace App\Infrastructure\Services\DatePeriods;

use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;

class AssigneePeriodsFormatterService
{
    protected $assigneeRepository;
    protected $projectRepository;

    public function __construct(
        EloquentAssigneeRepository $assigneeRepository,
        EloquentProjectRepository $projectRepository,
    ) {
        $this->assigneeRepository = $assigneeRepository;
        $this->projectRepository = $projectRepository;
    }

    public function formatAssigneePeriods()
    {
        $projects = $this->projectRepository->allFutureProjectsWithDatePeriods();
        $assignees = $this->assigneeRepository->all();

        $formattedData = [
            'projects' => [],
            'assignees' => [],
        ];

        // Prepare assignees data
        foreach ($assignees as $assignee) {
            $formattedData['assignees'][$assignee->getId()] = [
                'id' => $assignee->getId(),
                'name' => $assignee->getName(),
            ];
        }

        // Prepare projects and date periods data
        foreach ($projects as $project) {
            $projectId = $project->getId();
            $formattedData['projects'][$projectId] = [
                'id' => $projectId,
                'name' => $project->getName(),
                'build_status' => $project->getBuildStatus(),
                'rag_status' => $project->getRagStatus(),
                'date_periods' => [],
            ];

            foreach ($project->getDatePeriods() as $datePeriod) {
                $formattedData['projects'][$projectId]['date_periods'][] = [
                    'start' => $datePeriod->getStartDate(),
                    'end' => $datePeriod->getEndDate(),
                    'assignee_id' => $datePeriod->getAssigneeId(),
                ];
            }
        }

        return $formattedData;
    }
}
