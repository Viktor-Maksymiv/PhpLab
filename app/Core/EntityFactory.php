<?php

declare(strict_types=1);

namespace App\Core;

use App\Entities\AbstractEntity;
use App\Entities\Movie;
use App\Entities\User;
use InvalidArgumentException;

class EntityFactory
{
    public static function createEntity(string $type, array $data): AbstractEntity
    {
        return match ($type) {
            'movie' => new Movie($data['id'], $data['createdAt'], $data['title'], $data['durationMinutes'], $data['ageRating']),
            'user' => new User($data['id'], $data['createdAt'], $data['name'], $data['email'], $data['role']),
            default => throw new InvalidArgumentException("Невідомий тип сутності: {$type}"),
        };
    }
}
