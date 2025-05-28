<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use App\Http\Requests\StoreUserSettingRequest;
use App\Http\Requests\UpdateUserSettingRequest;


use Illuminate\Http\Request;
use App\Services\SettingsService;
use App\Settings\UserSettingDefinition;

class UserSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SettingsService $settings)
    {
        $definition = UserSettingDefinition::all();

        // Get values for all settings, default fallback
        $values = [];
        foreach ($definition as $key => $_) {
            $values[$key] = $settings->get($key, auth()->user());
        }
        return view('setting.index', [
            'theme' => $settings->get('theme', auth()->user()),
            'sortColumn' => $settings->get('sort_column', auth()->user()),
            'sortOrder' => $settings->get('sort_order', auth()->user()),
            'definition' => $definition,
            'values' => $values,   
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserSetting $userSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserSetting $userSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserSettingRequest $request, SettingsService $settings)
    {
        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark'],
            'sort_column' => ['required', 'string'],
            'sort_order' => ['required', 'in:asc,desc'],
        ]);

        foreach ($validated as $key => $value) {
            $settings->set($key, $value, auth()->user());
        }

        return redirect()->route('setting.index')->with('success', 'Settings updated.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserSetting $userSetting)
    {
        //
    }
}
