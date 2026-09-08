<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = DB::table('app_settings')->get();

        foreach ($settings as $setting) {
            $raw = $setting->rotation_pages;
            if (empty($raw)) {
                continue;
            }

            $pages = is_string($raw) ? json_decode($raw, true) : $raw;
            if (!is_array($pages)) {
                continue;
            }

            $modified = false;
            foreach ($pages as &$page) {
                if (isset($page['url']) && ($page['url'] === 'welcome-embed' || $page['url'] === '/welcome-embed')) {
                    $page['active'] = false;
                    $modified = true;
                }
            }
            unset($page);

            if ($modified) {
                DB::table('app_settings')
                    ->where('id', $setting->id)
                    ->update(['rotation_pages' => json_encode($pages)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No strict reverse needed, can be toggled back in Admin Panel
    }
};
