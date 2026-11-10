<?php

declare(strict_types=1);

namespace App\Tasks;

class Artist
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $isBand,
        public readonly int $startYear
    ) {}
}
