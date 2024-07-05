<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\BankHoliday;
use App\Domain\Repositories\BankHolidayRepository;
use App\Infrastructure\Models\BankHolidayModel;
use Illuminate\Database\Eloquent\Collection;

class EloquentBankHolidayRepository implements BankHolidayRepository
{
    public function save(BankHoliday $bankHoliday): void
    {
        BankHolidayModel::updateOrCreate(
            ['date' => $bankHoliday->getDate()],
        );
    }

    public function list(): Collection
    {
        return BankHolidayModel::all()->map->toDomainEntity();
    }
}
