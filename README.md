# laravel-queue-api

Simple example of using queues via API in Laravel

## Installation

1. Open a terminal in the directory containing project
2. Run the command to build up all Docker containers: `make build-up`
3. Run command to open terminal in "php-cli" container: `make cli`:
    - and run command to create tables in database: `php artisan migrate`
    - and run command to install php dependencies: `composer install`

## Usage (api endpoints)

1. Add a new item to the queue (an "x" parameter is required): `POST http://localhost:8080/api/enqueue`
