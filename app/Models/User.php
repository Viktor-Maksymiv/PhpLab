<?php

declare(strict_types=1);

namespace App\Models;

class User extends AbstractEntity
{
    public function __construct(
        int $id,
        string $createdAt,
        private string $name,
        private string $email,
        private string $role
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Користувач #{$this->id}: {$this->name} ({$this->role}), {$this->email}";
    }
}
