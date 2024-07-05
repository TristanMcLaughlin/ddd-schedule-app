<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\BankHoliday;
use Illuminate\Database\Eloquent\Collection;

interface BankHolidayRepository
{
    public function save(BankHoliday $bankHoliday): void;
    public function list(): Collection;
}
