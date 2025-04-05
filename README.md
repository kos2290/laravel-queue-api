# laravel-queue-api

Simple example of using queues via API in Laravel

## Installation

1. Open terminal in directory with project
1. Run command to build and up all docker containers: `make build-up`
2. Run command to open terminal in "php-cli" container: `make cli`:
    - and run command to create tables in database `php artisan migrate`
    - and run command to build php dependencies `composer install`
