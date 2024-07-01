<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\Team;
use App\Domain\Repositories\AssigneeRepository;
use App\Domain\Repositories\TeamRepository;
use GuzzleHttp\Client;
use App\Domain\Entities\Assignee;

class JiraGraphQLService
{
    protected $client;
    protected $config;
    protected $assigneeRepository;
    protected $teamRepository;

    public function __construct(
        Client $client,
        AssigneeRepository $assigneeRepository,
        TeamRepository $teamRepository
    ) {
        $this->client = $client;
        $this->config = config('jira');
        $this->assigneeRepository = $assigneeRepository;
        $this->teamRepository = $teamRepository;
    }

    public function getTeams()
    {
        $query = <<<'GRAPHQL'
        query teamSearchV2($first: Int, $after: String, $organizationId: ID!, $filter: TeamSearchFilter, $siteId: String!) {
          team {
            teamSearch: teamSearchV2(
              first: $first
              after: $after
              organizationId: $organizationId
              filter: $filter
              siteId: $siteId
            ) @optIn(to: ["Team-search-v2"]) {
              nodes {
                team {
                  id
                  displayName
                  members {
                    nodes {
                      member {
                        accountId
                        accountStatus
                        name
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
        }
        GRAPHQL;

        $response = $this->client->post($this->config['endpoints']['graphql'] . '?q=teamSearchV2', [
            'json' => [
                'query' => $query,
                'variables' => [
                    'first' => 50,
                    'organizationId' => $this->config['organisationId'],
                    'siteId' => $this->config['site'],
                    'filter' => [
                        'query' => implode(',', $this->config['teams'])
                    ],
                ],
            ],
            'auth' => [$this->config['auth']['username'], $this->config['auth']['apiToken']]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data']['team']['teamSearch']['nodes'] ?? [];
    }

    public function syncTeams()
    {
        $teams = $this->getTeams();
        $teamNames = $this->config['teams'];

        foreach ($teams as $teamNode) {
            $teamData = $teamNode['team'];
            $team = new Team($teamData['id'], $teamData['displayName']);
            $this->teamRepository->save($team);

            foreach ($teamData['members']['nodes'] as $memberNode) {
                $member = $memberNode['member'];
                $assignee = new Assignee(
                    $member['accountId'],
                    $member['name'],
                    $member['extendedProfile']['jobTitle'] ?? null,
                    $teamData['id']
                );

                $this->assigneeRepository->save($assignee);
            }
        }
    }
}
