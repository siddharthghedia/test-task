## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/9.x/installation)

Alternative installation is possible without local dependencies relying on [Docker](https://github.com/gothinkster/laravel-realworld-example-app/blob/master/readme.md#docker).

Clone the repository

```
git clone {GIT REPO URL}
```

Switch to the repo folder

```
cd {to folder}
```

Install all the dependencies using composer

```
composer install
```

Copy the example env file and make the required configuration changes in the .env file

```
cp .env.example .env
```

Please don't forget to mention MERCHANT EMAIL in .env file in MERCHANT_MAIL
```
MERCHANT_MAIL=
```

Generate a new application key

```
php artisan key:generate
```

Run the database migrations (Set the database connection in .env before migrating)

```
php artisan migrate
```

Run the database seeder and you're done

```
php artisan db:seed
```

Start the local development server

```
php artisan serve
```

Run Test Cases:

```
php artisan test
```

POSTMAN COLLECTION:

```
https://api.postman.com/collections/2120981-d69a4428-97d7-409a-9a75-a6373dde6aff?access_key=PMAT-01GNW5JZEBWJDEJNKK0WN15FN0
```

