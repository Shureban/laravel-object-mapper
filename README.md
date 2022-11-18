# Laravel query searcher

## Installation

Require this package with composer using the following command:

```bash
composer require shureban/laravel-object-mapper
```

Add the following class to the `providers` array in `config/app.php`:

```php
Shureban\LaravelSearcher\ObjectMapperServiceProvider::class,
```

You can also publish the config file to change implementations (ie. interface to specific class).

```shell
php artisan vendor:publish --provider="Shureban\LaravelSearcher\ObjectMapperServiceProvider"
```

## How to use

- base types
- class with constructor
- class without countruector
- setters
