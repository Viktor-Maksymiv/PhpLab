<?php

declare(strict_types=1);

namespace App\Tasks;

class Student extends Person
{
    public function __construct(
        string $name,
        string $birthday,
        protected string $faculty
    ) {
        parent::__construct($name, $birthday);
    }

    public function getRoleDetails(): string
    {
        return "{$this->name} ({$this->birthday}): навчається на факультеті {$this->faculty}";
    }
}
