<?php

declare(strict_types=1);

namespace App\Tasks;

class Teacher extends Student
{
    public function __construct(
        string $name,
        string $birthday,
        string $faculty,
        protected string $subject
    ) {
        parent::__construct($name, $birthday, $faculty);
    }

    public function getRoleDetails(): string
    {
        return parent::getRoleDetails()."; викладає {$this->subject}";
    }
}
