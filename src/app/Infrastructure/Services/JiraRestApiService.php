<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\Project;
use App\Domain\Services\DatePeriod\DatePeriodContext;
use App\Domain\Strategies\DatePeriod\ConfigDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\DevDatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\QADatePeriodStrategy;
use App\Domain\Strategies\DatePeriod\QADevDatePeriodStrategy;
use App\Infrastructure\Repositories\EloquentDatePeriodRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;

class JiraRestApiService
{
    protected $client;
    protected $config;
    protected $projectRepository;
    protected $datePeriodRepository;
    private DatePeriodContext $datePeriodContext;

    public function __construct(
        Client $client,
        EloquentProjectRepository $projectRepository,
        EloquentDatePeriodRepository $datePeriodRepository,
        DatePeriodContext $datePeriodContext
    )
    {
        $this->client = $client;
        $this->config = config('jira');
        $this->projectRepository = $projectRepository;
        $this->datePeriodRepository = $datePeriodRepository;
        $this->datePeriodContext = $datePeriodContext;

        $this->initializeStrategies();
    }

    protected function initializeStrategies()
    {
        $this->datePeriodContext->addStrategy(new ConfigDatePeriodStrategy());
        $this->datePeriodContext->addStrategy(new DevDatePeriodStrategy());
        $this->datePeriodContext->addStrategy(new QADatePeriodStrategy());
        $this->datePeriodContext->addStrategy(new QADevDatePeriodStrategy());
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
                'fields' => 'id,key,summary,customfield_10015,customfield_10098,customfield_10099,customfield_10152,customfield_10166,status',
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

            $datePeriods = $this->datePeriodContext->createDatePeriods($project, $epic);
            foreach ($datePeriods as $datePeriod) {
                $this->datePeriodRepository->save($datePeriod);
            }
        }
    }
}
