<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\Project;
use App\Domain\Entities\BacklogTicket;
use App\Domain\Services\DatePeriod\DatePeriodContext;
use App\Domain\Strategies\DatePeriod\ConfigDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\ContentDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\DevDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\DueDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\PMDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\QADatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\QADevDatePeriodStrategy;
use App\Infrastructure\Repositories\EloquentDatePeriodRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use App\Infrastructure\Repositories\EloquentBacklogTicketRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;

class JiraRestApiService
{
    protected $client;
    protected $projectRepository;
    protected $datePeriodRepository;
    protected $backlogTicketRepository;

    public function __construct(
        Client $client,
        EloquentProjectRepository $projectRepository,
        EloquentDatePeriodRepository $datePeriodRepository,
        EloquentBacklogTicketRepository $backlogTicketRepository
    )
    {
        $this->client = $client;
        $this->projectRepository = $projectRepository;
        $this->datePeriodRepository = $datePeriodRepository;
        $this->backlogTicketRepository = $backlogTicketRepository;
    }

    protected function makeSearchRequest(string $jql, array $fields, int $maxResults = 300): array
    {
        $response = $this->client->get(config('jira.endpoints.rest') . '/search', [
            'query' => [
                'jql' => $jql,
                'fields' => implode(',', $fields),
                'maxResults' => $maxResults
            ],
            'auth' => [config('jira.auth.username'), config('jira.auth.apiToken')]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['issues'] ?? [];
    }

    public function getEpics(): array
    {
        $fourMonthsAgo = Carbon::now()->subMonths(4)->format('Y-m-d');
        $jql = "project = CAM AND issuetype = Epic AND created >= $fourMonthsAgo";
        $fields = [
            'id', 'key', 'summary', 'customfield_10015', 'customfield_10098',
            'customfield_10099', 'customfield_10112', 'customfield_10152',
            'customfield_10155', 'customfield_10158', 'customfield_10166', 'status'
        ];

        return $this->makeSearchRequest($jql, $fields);
    }

    public function getBacklogTickets(): array
    {
        $jql = 'project = DDD AND issuetype IN ("Change Request", "Defect") AND status NOT IN ("Done", "Abandoned") AND duedate >= -7d AND duedate <= 60d';
        $fields = ['summary', 'assignee', 'status', 'issuetype', 'created', 'priority', 'duedate', 'timeestimate'];

        return $this->makeSearchRequest($jql, $fields);
    }

    protected function createDatePeriodContext(): DatePeriodContext
    {
        $datePeriodContext = new DatePeriodContext();
        $datePeriodContext->addStrategy(new ConfigDatePeriodStrategy());
        $datePeriodContext->addStrategy(new DevDatePeriodStrategy());
        $datePeriodContext->addStrategy(new QADatePeriodStrategy());
        $datePeriodContext->addStrategy(new QADevDatePeriodStrategy());
        $datePeriodContext->addStrategy(new ContentDatePeriodStrategy());
        $datePeriodContext->addStrategy(new DueDatePeriodStrategy());
        $datePeriodContext->addStrategy(new PMDatePeriodStrategy());

        return $datePeriodContext;
    }

    public function syncProjects()
    {
        $epics = $this->getEpics();
        $datePeriodContext = $this->createDatePeriodContext();

        foreach ($epics as $epic) {
            $project = new Project(
                $epic['key'],
                $epic['fields']['summary'],
                $epic['fields']['status']['name'],
                $epic['fields']['customfield_10166']['value'] ?? ''
            );

            $this->projectRepository->save($project);

            $datePeriods = $datePeriodContext->createDatePeriods($project, $epic);
            foreach ($datePeriods as $datePeriod) {
                try {
                    $this->datePeriodRepository->save($datePeriod);
                } catch (\Exception $e) {
                    // Ideally this would log to Sentry
                    // So I should get a trial lol
                }
            }
        }
    }

    public function syncBacklogTickets()
    {
        $backlogTickets = $this->getBacklogTickets();

        foreach ($backlogTickets as $ticket) {
            try {
                $backlogTicket = new BacklogTicket(
                    $ticket['key'],
                    $ticket['fields']['assignee']['accountId'] ?? 'unassigned-developer',
                    $ticket['fields']['priority']['name'],
                    $ticket['fields']['summary'],
                    Carbon::parse($ticket['fields']['duedate'])->subSeconds($ticket['fields']['timeestimate'] ?? 0)->toDateString(),
                    $ticket['fields']['duedate'],
                    $ticket['fields']['status']['name'],
                );

                $this->backlogTicketRepository->save($backlogTicket);
            } catch (\Exception $e) {
                // Ideally this would log to Sentry
                // So I should get a trial lol
            }
        }
    }
}
