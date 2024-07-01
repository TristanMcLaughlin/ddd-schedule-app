<?php

namespace App\Domain\Entities;

readonly class Assignee
{
    public function __construct(
        private string  $id,
        private string  $name,
        private ?string $role,
        private ?string $teamId
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }
}
