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
}