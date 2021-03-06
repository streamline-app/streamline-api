# streamline-api
core backend api for streamline

## Download Composer
* Follow composer instructions : https://getcomposer.org/download/

## Check PHP Version
`php -v`
* We want at a PHP version that is v7.2.9 (Latest Stable Release)

## Install Laravel 
* Follow laravel documentation : https://laravel.com/docs/5.7/installation

## Download project dependencies
`composer install` or `composer.phar install` depending on if you have set an alias.

## Generate Project key
* You will need to mv .env.example to .env (this is your environment file)
  * mv .env.example .env
* Generate application key with `php artisan key:generate`

## Generate Secret key
* Generate the secret key with `php artisan jwt:secret`
* You will need this in order for auth to work

## Start up the project server
`php artisan serve`

## Run endpoint unit tests
* Ensure that you have a running instance of MySQL with correct credentials, refresh is optional
`php artisan migrate:refresh`
* Run unit tests (Must have phpunit installed globablly)
`phpunit`
