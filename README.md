# ITMOSOFT Laravel Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/itmosoft/laravel-settings.svg)](https://packagist.org/packages/itmosoft/laravel-settings)
[![License](https://img.shields.io/packagist/l/itmosoft/laravel-settings.svg)](LICENSE)

A lightweight, database-backed settings package for Laravel applications. Store, retrieve, update, and delete application-wide settings using a simple facade, with support for JSON-compatible values and bulk updates.

## Features

- Simple API for managing application settings.
- Support for strings, numbers, booleans, arrays, and `null`.
- Retrieve all settings with `all()`.
- Create or update multiple settings with `setMany()`.
- Configurable database table name.
- Automatic Laravel service provider discovery.
- Automated tests using Orchestra Testbench and SQLite.

## Requirements

- PHP 8.3 or later.
- Laravel 12 or Laravel 13.
- A database supported by Laravel.

## Installation

Install the package using Composer:

```bash
composer require itmosoft/laravel-settings
```

Run the database migrations:

```bash
php artisan migrate
```

Laravel automatically discovers the package service provider.

### Optional: Publish the configuration

Publish the package configuration if you want to customize the settings table name:

```bash
php artisan vendor:publish --tag=itmosoft-settings-config
```

This creates `config/itmosoft-settings.php`.

## Quick Start

Import the facade wherever you need to manage settings:

```php
use ItmoSoft\Settings\Facades\Settings;
```

Store a setting:

```php
Settings::set('site_name', 'ITMOSOFT');
```

Retrieve it:

```php
$siteName = Settings::get('site_name', 'My Website');
```

Update multiple settings:

```php
Settings::setMany([
    'site_name' => 'ITMOSOFT',
    'maintenance_mode' => false,
    'features' => ['blog', 'shop'],
]);
```

Retrieve all settings:

```php
$settings = Settings::all();
```

## API Reference

### `get(string $key, mixed $default = null): mixed`

Retrieve a setting by key. If the key does not exist, the supplied default is returned.

```php
$siteName = Settings::get('site_name', 'My Website');

$maintenanceMode = Settings::get('maintenance_mode', false);

$languages = Settings::get('supported_languages', ['en', 'ur']);
```

### `set(string $key, mixed $value): void`

Create a setting or update its existing value.

```php
Settings::set('site_name', 'ITMOSOFT');
Settings::set('maintenance_mode', false);
Settings::set('max_users', 100);
Settings::set('supported_languages', ['en', 'ur']);
Settings::set('optional_value', null);
```

Setting keys cannot be empty. Values must be JSON-encodable.

### `has(string $key): bool`

Check whether a setting exists.

```php
if (Settings::has('site_name')) {
    // The setting exists.
}
```

A setting whose value is `null` can still exist. Use `has()` when you need to distinguish a missing key from an existing key with a null value.

### `forget(string $key): bool`

Delete a setting.

```php
$deleted = Settings::forget('site_name');
```

Returns `true` if a record was deleted and `false` if the key did not exist.

### `all(): array`

Retrieve all settings as an associative array, ordered alphabetically by setting key.

```php
$settings = Settings::all();
```

Example result:

```php
[
    'features' => ['blog', 'shop'],
    'maintenance_mode' => false,
    'site_name' => 'ITMOSOFT',
]
```

Returns an empty array when no settings exist.

### `setMany(array $settings): void`

Create or update multiple settings in one bulk database operation.

```php
Settings::setMany([
    'site_name' => 'ITMOSOFT',
    'maintenance_mode' => false,
    'max_users' => 100,
    'features' => ['blog', 'shop'],
]);
```

An empty array is a no-op:

```php
Settings::setMany([]);
```

All values are JSON-encoded before the bulk write begins. Empty keys throw an `InvalidArgumentException`, and JSON encoding failures throw a `JsonException`.

## Practical Examples

### Feature flags

```php
Settings::set('features.blog_enabled', true);

if (Settings::get('features.blog_enabled', false)) {
    // Enable blog-related functionality.
}
```

### Application maintenance mode

```php
Settings::set('maintenance_mode', true);

if (Settings::get('maintenance_mode', false)) {
    // Display your maintenance page.
}
```

This package stores the setting; you are responsible for applying it to your application's middleware or request handling.

### Default values

```php
$itemsPerPage = Settings::get('items_per_page', 20);
```

The default is returned when the key is missing; it is not automatically saved to the database.

## Configuration

The default configuration is:

```php
return [
    'table' => 'itmosoft_settings',
];
```

After publishing the configuration, you can change the table name in `config/itmosoft-settings.php`.

The package migration uses the configured table name and creates the storage table when you run `php artisan migrate`.

## Development

Clone the repository and install its development dependencies:

```bash
git clone https://github.com/ITMOSOFT/laravel-settings.git
cd laravel-settings
composer install
```

Run the automated tests:

```bash
vendor/bin/phpunit
```

The test suite uses Orchestra Testbench and an in-memory SQLite database.

Continuous integration tests the package against Laravel 12 and Laravel 13.

## Scope

This package focuses on application-wide settings stored in a database.

It does not currently provide a settings dashboard, user-specific settings, caching, or permission management.

## Contributing

Bug reports, feature suggestions, and pull requests are welcome.

Before submitting a pull request, run the test suite and ensure all tests pass.

- [Report an issue](https://github.com/ITMOSOFT/laravel-settings/issues)
- [View the source code](https://github.com/ITMOSOFT/laravel-settings)

## License

This package is open-source software licensed under the [MIT License](LICENSE).
