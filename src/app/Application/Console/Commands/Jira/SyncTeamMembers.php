<?php

namespace App\Application\Console\Commands\Jira;

use Illuminate\Console\Command;
use App\Infrastructure\Services\JiraGraphQLService;

class SyncTeamMembers extends Command
{
    protected $signature = 'jira:sync-team-members';
    protected $description = 'Sync team members from Jira';

    protected $jiraGraphQLService;

    public function __construct(JiraGraphQLService $jiraGraphQLService)
    {
        parent::__construct();
        $this->jiraGraphQLService = $jiraGraphQLService;
    }

    public function handle()
    {
        $this->jiraGraphQLService->syncTeamMembers();
        $this->info('Team members synchronized successfully.');
    }
}
