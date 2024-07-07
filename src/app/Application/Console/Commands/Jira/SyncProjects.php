<?php

namespace App\Application\Console\Commands\Jira;

use Illuminate\Console\Command;
use App\Infrastructure\Services\JiraRestApiService;

class SyncProjects extends Command
{
    protected $signature = 'jira:sync-projects';
    protected $description = 'Sync projects (epics) from Jira';

    protected $jiraRestApiService;

    public function __construct(JiraRestApiService $jiraRestApiService)
    {
        parent::__construct();
        $this->jiraRestApiService = $jiraRestApiService;
    }

    public function handle()
    {
        $this->jiraRestApiService->syncProjects();
        $this->jiraRestApiService->syncBacklogTickets();
        $this->info('Projects (epics) synchronized successfully.');
    }
}
