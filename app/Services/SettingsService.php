<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserSetting;
use App\Settings\UserSettingDefinition;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, User $user = null)//: ?string
    {
        if (str_starts_with($key, 'global.')) {
            return $this->getGlobal($key);
        }

        // Check user-specific setting
        if ($user) {
            $userValue = UserSetting::where('user_id', $user->id)
                ->where('key', $key)
                ->value('value');

            if (!is_null($userValue)) {
                return $userValue;
            }
        }

        return $this->getGlobal($key);
    }

    protected function getGlobal(string $key)
    {
        $global = Cache::remember("settings.global.{$key}", 3600, function () use ($key) {
            return Setting::where('key', $key)->value('value');
        });

        return $global ?? $this->getCodeDefault($key);
    }

    public function set(string $key, $value, User $user): void
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
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

    protected function getCodeDefault(string $key)//: ?string
    {
        //dd(UserSettingDefinition::defaultValues()[$key] ?? null);
        return UserSettingDefinition::defaultValues()[$key] ?? null;
    }
}
