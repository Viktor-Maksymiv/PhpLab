<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\LoggerInterface;
use Exception;

class EntityService
{
    public function __construct(private LoggerInterface $logger) {}

    public function calculateTicketPrice(float $basePrice, float $multiplier): float
    {
        $price = $basePrice * $multiplier;
        $this->logger->log("Розраховано ціну квитка: {$price}");

        return $price;
    }

    public function applyDiscount(float $price, float $discount): float
    {
        if ($discount < 0 || $discount > 100) {
            $this->logger->log("Відхилено знижку: {$discount}");

            throw new Exception('Неприпустиме значення знижки!');
        }

        $result = $price - ($price * ($discount / 100));
        $this->logger->log("Застосовано знижку {$discount} %, ціна: {$result}");

        return $result;
    }
}
