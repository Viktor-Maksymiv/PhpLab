<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

class Movie extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        int $id,
        string $createdAt,
        private string $title,
        private int $durationMinutes,
        private string $ageRating
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Фільм #{$this->id}: {$this->title}, {$this->durationMinutes} хв, {$this->ageRating}";
    }
}
