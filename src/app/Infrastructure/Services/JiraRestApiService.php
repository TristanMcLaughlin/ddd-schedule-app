<?php

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;
use App\Domain\Entities\Project;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use Carbon\Carbon;

class JiraRestApiService
{
    protected $client;
    protected $config;
    protected $projectRepository;

    public function __construct(Client $client, EloquentProjectRepository $projectRepository)
    {
        $this->client = $client;
        $this->config = config('jira');
        $this->projectRepository = $projectRepository;
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
                'fields' => 'id,key,summary,customfield_10166,status',
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
        }
    }
}
