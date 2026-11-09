<?php

declare(strict_types=1);

function findMinDigit(int $number): int
{
    return (int) min(str_split((string) $number));
}

function swapFirstAndLastWord(string $text): string
{
    $words = explode(' ', trim($text));
    $last = count($words) - 1;

    if ($last < 1) {
        return $text;
    }

    [$words[0], $words[$last]] = [$words[$last], $words[0]];

    return implode(' ', $words);
}

function isSortedAscending(string $text): bool
{
    $chars = str_split($text);
    $sorted = $chars;
    sort($sorted, SORT_STRING);

    return $chars === $sorted;
}
