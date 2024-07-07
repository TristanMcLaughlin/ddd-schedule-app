<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\BacklogTicket;
use Illuminate\Support\Collection;

interface BacklogTicketRepository
{
    public function save(BacklogTicket $backlogTicket): void;
    public function list(): Collection;
}
