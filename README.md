# project_1cs_api

## Api documentation package

    https://github.com/rakutentech/laravel-request-docs

## Install dependencies

    composer install

## Change .env.example to .env and write database infos

    cp .env.example .env

## Generate an app key

    php artisan key:generate

## Launch database migrations if there are new ones

    php artisan migrate:refresh --seed

## To start server

    php artisan ser

## Some common commands

### List all commands

    php artisan list

### Command Help

    php artisan help migrate

### Route list

    php artisan route:list

### Run database migrations

    php artisan migrate

### Create table migration

    php artisan make:migration create_products_table

### Run database seeders

    php artisan db:seed

### Create middlware

    php artisan make:middlware AuthMiddleware

### Create model

-m (migration), -c (controller), -r (resource controllers), -f (factory), -s (seed) <br>
php artisan make:model Product -mcf

### Create controller

    php artisan make:controller ProductsController

### Create resource

    php artisan make:resource ProductResource

### Create factory

    php artisan make:factory ProductFactory

### Create seeder

    php artisan make:seeder ProductSeeder

### Create mail

    php artisan make:mail OrderShipped

### Create notification

    php artisan make:notification InvoicePaid

### Create event

    php artisan make:event PodcastProcessed

### Create listener

    php artisan make:listener SendPodcastNotification --event=PodcastProcessed

### Create request

    php artisan make:request LoginRequest

### Create policy

    php artisan make:policy UserPolicy
