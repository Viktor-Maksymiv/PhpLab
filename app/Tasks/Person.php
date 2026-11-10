<?php

declare(strict_types=1);

namespace App\Tasks;

abstract class Person
{
    public function __construct(
        protected string $name,
        protected string $birthday
    ) {}

    abstract public function getRoleDetails(): string;
}
