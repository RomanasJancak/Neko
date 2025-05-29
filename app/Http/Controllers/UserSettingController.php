<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use App\Http\Requests\StoreUserSettingRequest;
use App\Http\Requests\UpdateUserSettingRequest;


use Illuminate\Http\Request;
use App\Services\SettingsService;
use App\Settings\UserSettingDefinition;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SettingsService $settings)
    {
        $user = auth()->user();
        $definitions = UserSettingDefinition::defaultValues();
        $fullDefinition = UserSettingDefinition::all();

        // Get values for all settings, default fallback
        $values = [];
        foreach (array_keys($definitions) as $key) {
            $values[$key] = $settings->get($key, $user);
        }
        //dd($values);
        return view('setting.index', [
            //'theme' => $settings->get('theme', auth()->user()),
            //'sortColumn' => $settings->get('sort_column', auth()->user()),
            //'sortOrder' => $settings->get('sort_order', auth()->user()),
            'definition' => $definitions,
            'full' => $fullDefinition,
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
public function update(Request $request, SettingsService $settings)
{
    $input = $request->except('_token');
    $rules = [];

    // Flatten validation rules with form keys using underscores
    $flattenRules = function (array $definitions, string $prefix = '') use (&$flattenRules, &$rules) {
        foreach ($definitions as $key => $setting) {
            $currentKey = $prefix ? $prefix . '.' . $key : $key;

            // If this item has a 'rules' array, register validation rule
            if (isset($setting['rules'])) {
                $formKey = str_replace('.', '_', $currentKey);
                $rules[$formKey] = $setting['rules'];
            }

            // If nested deeper (no label means sub-settings), recurse
            if (is_array($setting) && !isset($setting['label'])) {
                $flattenRules($setting, $currentKey);
            }
        }
    };

    // Flatten all settings validation rules
    $flattenRules(UserSettingDefinition::all());

    // Validate with the flattened rules
    $validator = Validator::make($input, $rules);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    // Convert underscore keys back to dot notation for storage
    $validated = [];
    foreach ($validator->validated() as $key => $value) {
        $dotKey = str_replace('_', '.', $key);
        $validated[$dotKey] = $value;
    }

    // Save each setting for the authenticated user
    $user = auth()->user();
    foreach ($validated as $key => $value) {
        $settings->set($key, $value, $user);
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
