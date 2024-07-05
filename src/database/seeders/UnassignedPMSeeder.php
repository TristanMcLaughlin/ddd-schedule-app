<?php

namespace Database\Seeders;

use App\Domain\Entities\Assignee;
use App\Infrastructure\Repositories\EloquentAssigneeRepository;
use Illuminate\Database\Seeder;

class UnassignedPMSeeder extends Seeder
{
    public function run()
    {
        $repository = new EloquentAssigneeRepository();
        $repository->save(new Assignee(
            'unassigned-pm',
            'Unassigned PM',
            'Project Manager',
            'ari:cloud:identity::team/3c774bf9-529f-4a80-b4de-2e22f6d0e28e'
        ));
    }
}
