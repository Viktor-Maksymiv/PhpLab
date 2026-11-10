<?php

declare(strict_types=1);

namespace App\Tasks;

class Genre
{
    public function __construct(
        private array $artists,
        private int $genreId,
        private string $name
    ) {}

    public function addArtist(Artist $artist): void
    {
        $this->artists[$artist->id] = $artist;
    }

    public function removeArtist(int $id): void
    {
        unset($this->artists[$id]);
    }

    public function searchArtist(string $name): array
    {
        return array_filter($this->artists, fn (Artist $artist) => mb_stripos($artist->name, $name) !== false);
    }

    public function getArtists(): array
    {
        return $this->artists;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
