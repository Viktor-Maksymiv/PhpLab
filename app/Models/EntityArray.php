<?php

$genres = ['Thriller', 'Comedy', 'Drama', 'Animation', 'Horror'];
sort($genres);

$showtime = [
    'id' => 1,
    'movieId' => 12,
    'hallId' => 3,
    'startAt' => '2026-11-14 19:30:00',
    'basePrice' => 185.5,
    'status' => 'scheduled',
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Масиви: система продажу квитків</title>
</head>
<body>
    <h1>Жанри фільмів (відсортовано за алфавітом)</h1>
    <ul>
        <?php foreach ($genres as $genre) { ?>
            <li><?= $genre ?></li>
        <?php } ?>
    </ul>

    <h1>Сеанс (об'єкт класу Showtime)</h1>
    <ul>
        <?php foreach ($showtime as $key => $value) { ?>
            <li><?= $key ?>: <?= $value ?></li>
        <?php } ?>
    </ul>
</body>
</html>
