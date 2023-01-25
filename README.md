# Ultimania Server

Ultimania webservice, running publicly on [http://ultimania5.askuri.de](http://ultimania5.askuri.de/swagger)

## Requirements
- PHP 8.0
- MySQL or MariaDB
- Composer

## Installation

- Clone the repository
- `cd ultimania-server`
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- Set the values in .env
- `php artisan migrate`

## API docs

Specification of the API can be found in [resources/openapi/contract.yaml](resources/openapi/contract.yaml).
There is also a visual documentation generated from the spec available when the server is running under /swagger. 

## Running tests

Tests can be run inside IntelliJ (select `phpunit.xml` as run configuration) or from command line (`php artisan test`).
