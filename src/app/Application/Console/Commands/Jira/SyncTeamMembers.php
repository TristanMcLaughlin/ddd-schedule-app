<?php

namespace App\Application\Console\Commands\Jira;

use Database\Seeders\UnassignedDeveloperSeeder;
use Illuminate\Console\Command;
use App\Infrastructure\Services\JiraGraphQLService;
use Illuminate\Support\Facades\Artisan;

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
        $this->jiraGraphQLService->syncTeams();
        Artisan::call('db:seed', ['--class' => UnassignedDeveloperSeeder::class]);
        $this->info('Team members synchronized successfully.');
    }
}
