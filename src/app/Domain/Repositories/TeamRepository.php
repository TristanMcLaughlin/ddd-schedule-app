<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Team;

interface TeamRepository
{
    public function save(Team $team);
}
