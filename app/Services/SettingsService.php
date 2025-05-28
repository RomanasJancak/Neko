<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserSetting;
use App\Settings\UserSettingDefinition;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, User $user = null): ?string
    {
        // Check user-specific setting
        if ($user) {
            $userValue = UserSetting::where('user_id', $user->id)
                ->where('key', $key)
                ->value('value');

            if (!is_null($userValue)) {
                return $userValue;
            }
        }

        // Check global cached value
        $global = Cache::remember("settings.global.{$key}", 3600, function () use ($key) {
            return Setting::where('key', $key)->value('value');
        });

        // Return global or fallback to hardcoded default
        return $global ?? $this->getCodeDefault($key);
    }

    public function set(string $key, string $value, User $user): void
    {
        UserSetting::updateOrCreate(
            ['user_id' => $user->id, 'key' => $key],
            ['value' => $value]
        );
    }

    public function setGlobal(string $key, string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("settings.global.{$key}");
    }

    protected function getCodeDefault(string $key): ?string
    {
        return UserSettingDefinition::defaultValues()[$key] ?? null;
    }
}
