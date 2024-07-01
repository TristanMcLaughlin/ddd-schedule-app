<?php

namespace App\Infrastructure\Services\DatePeriods;

use App\Domain\Entities\Assignee;
use App\Domain\Repositories\AssigneeRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TeamRepository;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;

class AssigneePeriodsFormatterService
{
    protected $teamRespository;
    protected $projectRepository;

    public function __construct(
        TeamRepository $teamRespository,
        ProjectRepository $projectRepository,
    ) {
        $this->teamRespository = $teamRespository;
        $this->projectRepository = $projectRepository;
    }

    public function formatAssigneePeriods()
    {
        $projects = $this->projectRepository->allFutureProjectsWithDatePeriods();
        $teams = $this->teamRespository->all();

        $formattedData = [
            'projects' => [],
            'teams' => [],
        ];

        // Prepare assignees data
        foreach ($teams as $team) {
            $formattedData['teams'][] = [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'assignees' => array_map(fn (Assignee $assignee) => [
                    'id' => $assignee->getId(),
                    'name' => $assignee->getName(),
                ], $team->getAssignees()),
            ];
        }

        // Prepare projects and date periods data
        foreach ($projects as $project) {
            $projectId = $project->getId();
            $formattedData['projects'][] = [
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
