<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\EntityFactory;
use App\Interfaces\ExportableInterface;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Services\FileLogger;
use App\Tasks\Artist;
use App\Tasks\DepartmentHead;
use App\Tasks\Genre;
use App\Tasks\Student;
use App\Tasks\Teacher;
use App\Tasks\Triangle;

require_once 'vendor/autoload.php';

echo '--- Singleton ---', PHP_EOL;
$db = Database::getInstance();
$sameDb = Database::getInstance();
echo $db === $sameDb ? 'Екземпляр єдиний' : 'Екземпляри різні', PHP_EOL;

echo PHP_EOL, '--- Factory та абстрактний клас ---', PHP_EOL;
$movie = EntityFactory::createEntity('movie', [
    'id' => 1,
    'createdAt' => '2026-11-09',
    'title' => 'Inception',
    'durationMinutes' => 148,
    'ageRating' => '12+',
]);
$user = EntityFactory::createEntity('user', [
    'id' => 2,
    'createdAt' => '2026-11-09',
    'name' => 'Viktor',
    'email' => 'viktor@example.com',
    'role' => 'customer',
]);
echo $movie->getDetails(), PHP_EOL;
echo $user->getDetails(), PHP_EOL;

echo PHP_EOL, '--- Інтерфейс і трейт: експорт у JSON ---', PHP_EOL;
if ($movie instanceof ExportableInterface) {
    echo $movie->exportToJson(), PHP_EOL;
}

echo PHP_EOL, '--- Dependency Injection ---', PHP_EOL;
$service = new EntityService(new ConsoleLogger);
echo $service->calculateTicketPrice(150.0, 1.5), PHP_EOL;
echo $service->applyDiscount(1000, 10), PHP_EOL;

try {
    echo $service->applyDiscount(1000, 150), PHP_EOL;
} catch (Exception $e) {
    echo 'Помилка: ', $e->getMessage(), PHP_EOL;
}

$fileService = new EntityService(new FileLogger);
echo $fileService->calculateTicketPrice(200.0, 2.0), PHP_EOL;

echo PHP_EOL, '--- Завдання 6.1: жанр та виконавці ---', PHP_EOL;
$genre = new Genre([], 1, 'Rock');
$genre->addArtist(new Artist(1, 'Queen', true, 1970));
$genre->addArtist(new Artist(2, 'David Bowie', false, 1964));
$genre->addArtist(new Artist(3, 'Led Zeppelin', true, 1968));
echo 'Жанр ', $genre->getName(), ', виконавців: ', count($genre->getArtists()), PHP_EOL;

foreach ($genre->searchArtist('led') as $artist) {
    echo 'Знайдено: ', $artist->name, ', початок діяльності ', $artist->startYear, PHP_EOL;
}

$genre->removeArtist(2);
echo 'Після видалення виконавців: ', count($genre->getArtists()), PHP_EOL;

echo PHP_EOL, '--- Завдання 6.2: ієрархія ---', PHP_EOL;
$people = [
    new Student('Іван Мельник', '2005-09-01', 'інформатики'),
    new Teacher('Петро Шевчук', '1980-06-20', 'інформатики', 'Бази даних'),
    new DepartmentHead('Олена Коваль', '1975-03-14', 'інформатики', 'Веб-програмування', 'комп’ютерних наук'),
];

foreach ($people as $person) {
    echo $person->getRoleDetails(), PHP_EOL;
}

echo PHP_EOL, '--- Завдання 6.3: трикутник ---', PHP_EOL;
$triangle = new Triangle(3, 4, 5);
echo 'Периметр: ', $triangle->getPerimeter(), PHP_EOL;
echo 'Площа: ', $triangle->getArea(), PHP_EOL;

try {
    new Triangle(1, 2, 10);
} catch (InvalidArgumentException $e) {
    echo 'Помилка: ', $e->getMessage(), PHP_EOL;
}
