# ITMOSOFT Laravel Settings

A reusable, database-backed application settings package for Laravel.

## Requirements

-   PHP 8.3+
-   Laravel 12
-   A database supported by Laravel

## Installation

Install the package through Composer:

``` bash
composer require itmosoft/laravel-settings
```

Run the package migrations:

``` bash
php artisan migrate
```

Optionally publish the package configuration:

``` bash
php artisan vendor:publish --tag=itmosoft-settings-config
```

The configuration file will be created at
`config/itmosoft-settings.php`.

## Usage

Import the facade:

``` php
use ItmoSoft\Settings\Facades\Settings;
```

### Store a setting

``` php
Settings::set('site_name', 'ITMOSOFT');
Settings::set('maintenance_mode', false);
Settings::set('supported_languages', ['en', 'ur']);
```

### Retrieve a setting

``` php
$siteName = Settings::get('site_name', 'My Website');

$maintenanceMode = Settings::get('maintenance_mode', false);

$languages = Settings::get('supported_languages', ['en']);
```

### Check whether a setting exists

``` php
if (Settings::has('site_name')) {
    // The setting exists.
}
```

### Delete a setting

``` php
$deleted = Settings::forget('site_name');
```

`forget()` returns `true` when a record was deleted and `false` when the
key did not exist.


### Retrieve all settings

Retrieve all settings as an associative array. The result is ordered
alphabetically by setting key.

```php
$settings = Settings::all();

// Example:
// [
//     'maintenance_mode' => false,
//     'site_name' => 'ITMOSOFT',
//     'supported_languages' => ['en', 'ur'],
// ]
```

If no settings exist, `all()` returns an empty array.

### Store multiple settings

Use `setMany()` to create or update several settings in one bulk
database operation.

```php
Settings::setMany([
    'site_name' => 'ITMOSOFT',
    'maintenance_mode' => false,
    'supported_languages' => ['en', 'ur'],
    'max_users' => 100,
]);
```

An empty array is a no-op:

```php
Settings::setMany([]);
```

Setting keys cannot be empty. Values must be JSON-encodable; invalid
values throw a `JsonException`. All values are encoded before the
bulk database write begins.


## Configuration

The default configuration is:

``` php
return [
    'table' => 'itmosoft_settings',
];
```

You can change the table name in `config/itmosoft-settings.php` after
publishing the configuration.

## Development

Install development dependencies:

``` bash
composer install
```

Run the automated tests:

``` bash
./vendor/bin/phpunit
```

The test suite uses Orchestra Testbench and an in-memory SQLite
database.

## Current scope

Version 1 focuses on simple application-wide settings. It does not
currently provide a settings dashboard, user-specific settings, caching,
or permission management.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
