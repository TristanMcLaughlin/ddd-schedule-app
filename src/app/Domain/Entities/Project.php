<?php

namespace App\Domain\Entities;

readonly class Project
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $buildStatus,
        private readonly string $ragStatus,
        private readonly array $datePeriods
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBuildStatus(): string
    {
        return $this->buildStatus;
    }

    public function getRagStatus(): string
    {
        return $this->ragStatus;
    }

    public function getDatePeriods(): array
    {
        return $this->datePeriods;
    }
}
