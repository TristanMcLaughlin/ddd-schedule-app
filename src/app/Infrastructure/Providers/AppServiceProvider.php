<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\AssigneeRepository;
use App\Domain\Repositories\BankHolidayRepository;
use App\Domain\Repositories\DatePeriodRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Domain\Repositories\TeamRepository;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use App\Infrastructure\Repositories\EloquentBankHolidayRepository;
use App\Infrastructure\Repositories\EloquentDatePeriodRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;
use App\Infrastructure\Repositories\EloquentTeamRepository;
use App\Infrastructure\Services\DatePeriods\AssigneePeriodsFormatterService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
        $this->app->bind(AssigneeRepository::class, EloquentAssigneeRepository::class);
        $this->app->bind(DatePeriodRepository::class, EloquentDatePeriodRepository::class);
        $this->app->bind(TeamRepository::class, EloquentTeamRepository::class);
        $this->app->bind(BankHolidayRepository::class, EloquentBankHolidayRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
