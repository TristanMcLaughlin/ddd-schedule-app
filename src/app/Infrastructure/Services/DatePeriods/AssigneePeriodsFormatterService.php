<?php

namespace App\Infrastructure\Services\DatePeriods;

use App\Domain\Entities\Assignee;
use App\Domain\Repositories\BacklogTicketRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TeamRepository;

readonly class AssigneePeriodsFormatterService
{
    public function __construct(
        protected TeamRepository          $teamRespository,
        protected ProjectRepository       $projectRepository,
        protected BacklogTicketRepository $backlogTicketRepository,
    ) {}

    public function formatAssigneePeriods()
    {
        $projects = $this->projectRepository->allFutureProjectsWithDatePeriods();
        $teams = $this->teamRespository->all();
        $backlogTickets = $this->backlogTicketRepository->list();

        $formattedData = [
            'projects' => [],
            'teams' => [],
            'backlog_tickets' => [],
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

        foreach ($backlogTickets as $ticket) {
            $formattedData['backlog_tickets'][] = [
                'id' => $ticket->getTicketId(),
                'assignee_id' => $ticket->getAssigneeId(),
                'priority' => $ticket->getPriority(),
                'summary' => $ticket->getSummary(),
                'start_date' => $ticket->getStartDate(),
                'end_date' => $ticket->getEndDate(),
            ];
        }

        return $formattedData;
    }
}
