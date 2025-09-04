<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingStore
{
    public function get(string $key, $default = null)
    {
        // Prefer site_settings (id,key,value,timestamps)
        if (Schema::hasTable('site_settings')) {
            $row = DB::table('site_settings')->where('key', $key)->value('value');
            if (!is_null($row)) return $row;
        }

        // Fallback to legacy settings (key,value)
        if (Schema::hasTable('settings')) {
            $row = DB::table('settings')->where('key', $key)->value('value');
            if (!is_null($row)) return $row;
        }

        return $default;
    }

    public function all(): array
    {
        $out = [];
        if (Schema::hasTable('settings')) {
            foreach (DB::table('settings')->select('key', 'value')->get() as $r) {
                $out[$r->key] = $r->value;
            }
        }
        if (Schema::hasTable('site_settings')) {
            foreach (DB::table('site_settings')->select('key', 'value')->get() as $r) {
                $out[$r->key] = $r->value; // site_settings overrides settings
            }
        }
        return $out;
    }
}
