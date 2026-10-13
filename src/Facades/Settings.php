<?php

namespace ItmoSoft\Settings\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static bool has(string $key)
 * @method static bool forget(string $key)
 *
 * @see \ItmoSoft\Settings\Settings
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'itmosoft.settings';
    }
}