<?php

return [
    'auth' => [
      'username' => env('JIRA_USERNAME'),
      'apiToken' => env('JIRA_API_TOKEN'),
    ],
    'endpoints' => [
        'graphql' => env('JIRA_GRAPHQL_ENDPOINT'),
        'rest' => env('JIRA_REST_ENDPOINT'),
    ],
    'team' => env('JIRA_TEAM_ID'),
    'site' => env('JIRA_SITE_ID'),
];
