<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\AssigneeRepository;
use App\Domain\Repositories\DatePeriodRepository;
use App\Domain\Repositories\ProjectRepository;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use App\Infrastructure\Repositories\EloquentDatePeriodRepository;
use App\Infrastructure\Repositories\EloquentProjectRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
