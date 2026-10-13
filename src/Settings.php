<?php

namespace ItmoSoft\Settings;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;

class Settings
{
    private function table(): string
    {
        return config('itmosoft-settings.table', 'itmosoft_settings');
    }

    /**
     * Retrieve a setting, or return the default if it doesn't exist.
     *
     * @throws JsonException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = DB::table($this->table())
            ->where('key', $key)
            ->first();

        if ($setting === null) {
            return $default;
        }

        return json_decode(
            $setting->value,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * Create or update a setting.
     *
     * @throws JsonException
     */
    public function set(string $key, mixed $value): void
    {
        if ($key === '') {
            throw new InvalidArgumentException(
                'The setting key cannot be empty.'
            );
        }

        $encodedValue = json_encode($value, JSON_THROW_ON_ERROR);
        $now = now();

        DB::table($this->table())->upsert(
            [[
                'key' => $key,
                'value' => $encodedValue,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['key'],
            ['value', 'updated_at']
        );
    }

    /**
     * Determine whether a setting exists.
     */
    public function has(string $key): bool
    {
        return DB::table($this->table())
            ->where('key', $key)
            ->exists();
    }

    /**
     * Delete a setting.
     */
    public function forget(string $key): bool
    {
        return DB::table($this->table())
            ->where('key', $key)
            ->delete() > 0;
    }
}