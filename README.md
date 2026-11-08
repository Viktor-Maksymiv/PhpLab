# CI/CD Lab

[![PHP CI Application](https://github.com/Viktor-Maksymiv/PhpLab/actions/workflows/php-ci.yml/badge.svg?branch=main)](https://github.com/Viktor-Maksymiv/PhpLab/actions/workflows/php-ci.yml)

Мінімальний PHP-проєкт для демонстрації безперервної інтеграції.


## Функціонал


- клас Calculator з методами add та subtract
- модульні тести PHPUnit
- перевірка стилю коду лінтером Pint
- автоматичний запуск пайплайну в GitHub Actions


## Запуск тестів


```bash
composer install
./vendor/bin/pint --test
./vendor/bin/phpunit tests/Unit
```