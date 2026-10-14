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

    /**
     * Retrieve all settings as a key-value array.
     *
     * @throws JsonException
     */
    public function all(): array
    {
        $settings = DB::table($this->table())
            ->orderBy('key')
            ->get(['key', 'value']);

        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = json_decode(
                $setting->value,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return $result;
    }

    /**
     * Create or update multiple settings.
     *
     * Values are encoded before any database write is performed.
     *
     * @throws InvalidArgumentException
     * @throws JsonException
     */
    public function setMany(array $settings): void
    {
        if ($settings === []) {
            return;
        }

        $now = now();
        $rows = [];

        foreach ($settings as $key => $value) {
            $key = (string) $key;

            if ($key === '') {
                throw new InvalidArgumentException(
                    'The setting key cannot be empty.'
                );
            }

            $rows[] = [
                'key' => $key,
                'value' => json_encode($value, JSON_THROW_ON_ERROR),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table($this->table())->upsert(
            $rows,
            ['key'],
            ['value', 'updated_at']
        );
    }
}
