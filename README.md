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

## Setup MySQL Database Locally
* Install mysql : https://www.mysql.com/downloads/
* Open mysql `mysql -uroot`
* Create a database called streamline `create database streamline`
* In the project, edit .env file
  * Change `DB_DATABASE` to streamline
  * Change `DB_USERNAME` to root
  * Change `DB_PASSWORD` to your root password.
  
## Run database mirgration
* Database migrations build up the database schema on your local machine. 
* To run migrations run `php artisan migrate`
* To rollback the migrations run `php artisan migrate:rollback`

## Start up the project server
`php artisan serve`
