<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\DatePeriod;
use App\Infrastructure\Repositories\EloquentDatePeriodRepository;
use GuzzleHttp\Client;
use App\Domain\Entities\Project;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use Carbon\Carbon;

class JiraRestApiService
{
    protected $client;
    protected $config;
    protected $projectRepository;
    protected $datePeriodRepository;

    public function __construct(
        Client $client,
        EloquentProjectRepository $projectRepository,
        EloquentDatePeriodRepository $datePeriodRepository,
    )
    {
        $this->client = $client;
        $this->config = config('jira');
        $this->projectRepository = $projectRepository;
        $this->datePeriodRepository = $datePeriodRepository;
    }

    public function getEpics()
    {
        // Calculate the date 4 months ago
        $fourMonthsAgo = Carbon::now()->subMonths(4)->format('Y-m-d');

        // JQL query to find epics in the CAM project created within the last 4 months
        $jql = "project = CAM AND issuetype = Epic AND created >= $fourMonthsAgo";

        $response = $this->client->get($this->config['endpoints']['rest'] . '/search', [
            'query' => [
                'jql' => $jql,
                'fields' => 'id,key,summary,customfield_10015,customfield_10098,customfield_10152,customfield_10166,status',
                'maxResults' => 300
            ],
            'auth' => [$this->config['auth']['username'], $this->config['auth']['apiToken']]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['issues'] ?? [];
    }

    public function syncProjects()
    {
        $epics = $this->getEpics();

        foreach ($epics as $epic) {
            $project = new Project(
                $epic['key'], // id
                $epic['fields']['summary'], // name
                $epic['fields']['status']['name'], // build status
                $epic['fields']['customfield_10166']['value'] ?? '' // rag status
            );

            $this->projectRepository->save($project);

            // Create Date Periods
            $startDate = $epic['fields']['customfield_10015'];
            $qaDueDate = $epic['fields']['customfield_10098'];
            $developerId = $epic['fields']['customfield_10152']['accountId'] ?? 'unassigned-developer';

            // Config Date Period
            $configStartDate = Carbon::parse($startDate)->addDay()->format('Y-m-d');
            $configEndDate = Carbon::parse($configStartDate)->format('Y-m-d');

            $configPeriod = new DatePeriod(
                uniqid(),
                $project->getId(),
                '63fca0987655a3223a217054', // Hardcoded config assignee ID
                $configStartDate,
                $configEndDate
            );

            $this->datePeriodRepository->save($configPeriod);

            // Development Date Period
            $devStartDate = Carbon::parse($startDate)->addDay()->format('Y-m-d');
            $devEndDate = Carbon::parse($qaDueDate)->format('Y-m-d');

            $devPeriod = new DatePeriod(
                uniqid(),
                $project->getId(),
                $developerId,
                $devStartDate,
                $devEndDate
            );

            $this->datePeriodRepository->save($devPeriod);
        }
    }
}
