<?php

namespace App\Domain\Entities;

readonly class Assignee
{
    public function __construct(
        private string  $id,
        private string  $name,
        private string  $email,
        private ?string $role
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
}
