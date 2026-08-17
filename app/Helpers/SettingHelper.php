<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * Mengambil nilai setting berdasarkan key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Menyimpan atau memperbarui setting.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Mengambil semua setting dalam bentuk array key => value.
     *
     * @return array
     */
    public static function all(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }
}