<?php

namespace App\Infrastructure\Services;

use GuzzleHttp\Client;
use App\Domain\Entities\Assignee;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;

class JiraGraphQLService
{
    protected $client;
    protected $config;
    protected $assigneeRepository;

    public function __construct(Client $client, EloquentAssigneeRepository $assigneeRepository)
    {
        $this->client = $client;
        $this->config = config('jira');
        $this->assigneeRepository = $assigneeRepository;
    }

    public function getTeamMembers()
    {
        $query = <<<'GRAPHQL'
        query TeamMembership($teamId: ID!, $siteId: String!, $first: Int!, $after: String) {
          team {
            teamV2(
              id: $teamId
              siteId: $siteId
            ) @optIn(to: "Team-v2") {
              members(first: $first, after: $after) {
                edges {
                  node {
                    member {
                      accountId
                      name
                      accountStatus
                      ... on AtlassianAccountUser {
                        extendedProfile {
                          jobTitle
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        GRAPHQL;

        $variables = [
            'teamId' => $this->config['team'],
            'siteId' => $this->config['site'],
            'first' => 100,
        ];

        $response = $this->client->post($this->config['endpoints']['graphql'] . '?q=TeamMembership', [
            'json' => [
                'query' => $query,
                'variables' => $variables
            ],
            'auth' => [$this->config['auth']['username'], $this->config['auth']['apiToken']]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data']['team']['teamV2']['members']['edges'] ?? [];
    }

    public function syncTeamMembers()
    {
        $members = $this->getTeamMembers();

        foreach ($members as $memberEdge) {
            $member = $memberEdge['node']['member'];

            $assignee = new Assignee(
                $member['accountId'],
                $member['name'],
                $member['extendedProfile']['jobTitle'] ?? null
            );

            $this->assigneeRepository->save($assignee);
        }
    }
}
