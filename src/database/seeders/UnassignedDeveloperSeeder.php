<?php

namespace Database\Seeders;

use App\Domain\Entities\Assignee;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use Illuminate\Database\Seeder;

class UnassignedDeveloperSeeder extends Seeder
{
    public function run()
    {
        $repository = new EloquentAssigneeRepository();
        $repository->save(new Assignee(
            'unassigned-developer',
            'Unassigned Developer',
            'Web Developer',
            null
        ));
    }
}
