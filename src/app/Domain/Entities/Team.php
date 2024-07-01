<?php

namespace App\Domain\Entities;

readonly class Team
{
    public function __construct(
        private string $id,
        private string $name,
        private array $assignees = []
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Assignee[]
     */
    public function getAssignees(): array
    {
        return $this->assignees;
    }
}
