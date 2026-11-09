<?php

declare(strict_types=1);
use Faker\Factory;

require_once 'vendor/autoload.php';
require_once 'app/Services/EntityService.php';
require_once 'app/Services/DataProcessor.php';

$faker = Factory::create();
$prices = [];

for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->numberBetween(80, 400);
}

$minThreshold = 200;

$expensiveTickets = array_filter($prices, function ($price) use ($minThreshold) {
    return $price > $minThreshold;
});

$ticketPrice = calculateTicketPrice(150.0, 1.5);

$number = 1234076;
$text = 'Квиток на ранковий сеанс у залі IMAX';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Система продажу квитків до кінотеатру</title>
</head>
<body>
    <h1>Електронна система продажу квитків до кінотеатру</h1>
    <p><a href="app/Models/EntityArray.php">Завдання 1: масиви</a></p>

    <h2>Завдання 2: строга типізація</h2>
    <p>Ціна квитка (базова ціна 150, коефіцієнт місця 1.5): <?= $ticketPrice ?></p>
    <p>Ціна зі знижкою 10 %: <?= applyDiscount($ticketPrice, 10) ?></p>

    <h2>Завдання 3: Faker та замикання</h2>
    <p>Згенеровані ціни квитків: <?= implode(', ', $prices) ?></p>
    <p>Поріг: <?= $minThreshold ?></p>
    <p>Квитки дорожчі за поріг: <?= implode(', ', $expensiveTickets) ?></p>

    <h2>Завдання 4: обробка винятків</h2>
    <?php
    try {
        echo '<p>', applyDiscount(1000, 150), '</p>';
    } catch (Exception $e) {
        echo '<p>Помилка: ', $e->getMessage(), '</p>';
    } finally {
        echo '<p>Завершення спроби розрахунку.</p>';
    }
?>

    <h2>Завдання 5: обробка даних (варіант 2)</h2>
    <p>Мінімальна цифра числа <?= $number ?>: <?= findMinDigit($number) ?></p>
    <p>Мінімальна цифра числа 77777: <?= findMinDigit(77777) ?></p>
    <p>Вхідний рядок: <?= $text ?></p>
    <p>Перше й останнє слово поміняно місцями: <?= swapFirstAndLastWord($text) ?></p>
    <p>Рядок abcdxyz упорядкований за зростанням: <?= isSortedAscending('abcdxyz') ? 'так' : 'ні' ?></p>
    <p>Рядок ticket упорядкований за зростанням: <?= isSortedAscending('ticket') ? 'так' : 'ні' ?></p>
</body>
</html>
