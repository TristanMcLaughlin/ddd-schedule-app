<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnassignedDeveloperSeeder extends Seeder
{
    public function run()
    {
        DB::table('assignees')->insert([
            'id' => 'unassigned-developer',
            'name' => 'Unassigned Developer',
            'role' => 'Web Developer',
        ]);
    }
}
