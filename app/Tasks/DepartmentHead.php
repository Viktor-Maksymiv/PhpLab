<?php

declare(strict_types=1);

namespace App\Tasks;

class DepartmentHead extends Teacher
{
    public function __construct(
        string $name,
        string $birthday,
        string $faculty,
        string $subject,
        protected string $department
    ) {
        parent::__construct($name, $birthday, $faculty, $subject);
    }

    public function getRoleDetails(): string
    {
        return parent::getRoleDetails()."; завідує кафедрою {$this->department}";
    }
}
