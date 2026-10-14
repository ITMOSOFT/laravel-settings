<?php

namespace ItmoSoft\Settings\Tests\Feature;

use ItmoSoft\Settings\Facades\Settings;
use ItmoSoft\Settings\Tests\TestCase;

class SettingsTest extends TestCase
{
    public function test_it_can_store_and_retrieve_a_string(): void
    {
        Settings::set('site_name', 'ITMOSOFT');

        $this->assertSame('ITMOSOFT', Settings::get('site_name'));
    }

    public function test_it_returns_the_default_for_a_missing_key(): void
    {
        $this->assertSame(
            'Default Website',
            Settings::get('missing_key', 'Default Website')
        );
    }

    public function test_it_can_update_an_existing_setting(): void
    {
        Settings::set('site_name', 'Old Name');
        Settings::set('site_name', 'ITMOSOFT');

        $this->assertSame('ITMOSOFT', Settings::get('site_name'));
    }

    public function test_it_can_check_if_a_setting_exists(): void
    {
        Settings::set('site_name', 'ITMOSOFT');

        $this->assertTrue(Settings::has('site_name'));
        $this->assertFalse(Settings::has('missing_key'));
    }

    public function test_it_can_delete_a_setting(): void
    {
        Settings::set('site_name', 'ITMOSOFT');

        $this->assertTrue(Settings::forget('site_name'));
        $this->assertFalse(Settings::has('site_name'));
        $this->assertFalse(Settings::forget('site_name'));
    }

    public function test_it_preserves_json_compatible_data_types(): void
    {
        $values = [
            'string' => 'ITMOSOFT',
            'integer' => 123,
            'float' => 12.5,
            'boolean' => false,
            'array' => ['laravel', 'php'],
            'null' => null,
        ];

        foreach ($values as $key => $value) {
            Settings::set($key, $value);
        }

        foreach ($values as $key => $value) {
            $this->assertSame($value, Settings::get($key), "Failed for key: {$key}");
        }
    }

    public function test_it_rejects_an_empty_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Settings::set('', 'ITMOSOFT');
    }

    public function test_it_can_store_and_retrieve_an_empty_array(): void
    {
        Settings::set('empty_array', []);

        $this->assertSame([], Settings::get('empty_array'));
    }

    public function test_it_preserves_created_at_when_updating_a_setting(): void
    {
        Settings::set('site_name', 'Old Name');

        $createdAtBefore = \Illuminate\Support\Facades\DB::table(
            'itmosoft_settings'
        )->where('key', 'site_name')->value('created_at');

        Settings::set('site_name', 'New Name');

        $createdAtAfter = \Illuminate\Support\Facades\DB::table(
            'itmosoft_settings'
        )->where('key', 'site_name')->value('created_at');

        $this->assertSame($createdAtBefore, $createdAtAfter);
    }

    public function test_it_can_store_a_zero_value(): void
    {
        Settings::set('maintenance_mode', 0);

        $this->assertSame(0, Settings::get('maintenance_mode'));
        $this->assertTrue(Settings::has('maintenance_mode'));
    }

    public function test_it_rejects_invalid_utf8_values(): void
    {
        $this->expectException(\JsonException::class);

        Settings::set('invalid_value', "\xB1\x31");
    }

    public function test_it_can_retrieve_all_settings(): void
    {
        Settings::set('site_name', 'ITMOSOFT');
        Settings::set('maintenance_mode', false);
        Settings::set('features', ['blog', 'shop']);

        $this->assertSame([
            'features' => ['blog', 'shop'],
            'maintenance_mode' => false,
            'site_name' => 'ITMOSOFT',
        ], Settings::all());
    }

    public function test_all_returns_an_empty_array_when_no_settings_exist(): void
    {
        $this->assertSame([], Settings::all());
    }

    public function test_it_can_store_multiple_settings(): void
    {
        Settings::setMany([
            'site_name' => 'ITMOSOFT',
            'maintenance_mode' => false,
            'features' => ['blog', 'shop'],
            'max_users' => 100,
        ]);

        $this->assertSame('ITMOSOFT', Settings::get('site_name'));
        $this->assertSame(false, Settings::get('maintenance_mode'));
        $this->assertSame(['blog', 'shop'], Settings::get('features'));
        $this->assertSame(100, Settings::get('max_users'));
    }

    public function test_set_many_can_update_existing_settings(): void
    {
        Settings::set('site_name', 'Old Name');
        Settings::set('maintenance_mode', true);

        Settings::setMany([
            'site_name' => 'ITMOSOFT',
            'maintenance_mode' => false,
        ]);

        $this->assertSame('ITMOSOFT', Settings::get('site_name'));
        $this->assertSame(false, Settings::get('maintenance_mode'));
    }

    public function test_set_many_does_nothing_for_empty_input(): void
    {
        Settings::setMany([]);

        $this->assertSame([], Settings::all());
    }

    public function test_set_many_rejects_an_empty_key(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Settings::setMany([
            'site_name' => 'ITMOSOFT',
            '' => 'Invalid key',
        ]);
    }

    public function test_set_many_does_not_write_any_settings_when_encoding_fails(): void
    {
        try {
            Settings::setMany([
                'valid_setting' => 'This should not be stored',
                'invalid_setting' => "\xB1\x31",
            ]);

            $this->fail('Expected a JsonException to be thrown.');
        } catch (\JsonException) {
            $this->assertFalse(Settings::has('valid_setting'));
            $this->assertFalse(Settings::has('invalid_setting'));
        }
    }

    public function test_set_many_preserves_created_at_when_updating(): void
    {
        Settings::set('site_name', 'Old Name');

        $createdAtBefore = \Illuminate\Support\Facades\DB::table(
            'itmosoft_settings'
        )->where('key', 'site_name')->value('created_at');

        Settings::setMany([
            'site_name' => 'ITMOSOFT',
        ]);

        $createdAtAfter = \Illuminate\Support\Facades\DB::table(
            'itmosoft_settings'
        )->where('key', 'site_name')->value('created_at');

        $this->assertSame($createdAtBefore, $createdAtAfter);
    }
}
