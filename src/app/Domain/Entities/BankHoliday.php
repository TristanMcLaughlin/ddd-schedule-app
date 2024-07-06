<?php

namespace App\Domain\Entities;

readonly class BankHoliday
{
    private string $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->getDate(),
        ];
    }
}
