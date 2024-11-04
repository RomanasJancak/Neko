<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

use App\Models\Setting;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;

use App\Services\BackupService;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setting.index');
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
    public function store(StoreSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
    private function getAllModels($directory = 'App\Models')
    {
        $modelsPath = app_path(str_replace('\\', '/', str_replace('App\\', '', $directory)));
        $modelFiles = File::allFiles($modelsPath);
    
        $models = [];
        foreach ($modelFiles as $file) {
            $relativePath = $file->getRelativePathname();
    
            // Build the fully qualified class name
            $className = $directory . '\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
    
            // Check if the class exists and add it to the array if it does
            if (class_exists($className)) {
                $models[] = $className;
            }
        }
    
        return $models;
    }
    public function backupAll(){
        $models = $this->getAllModels();
        foreach ($models as $model) {
            BackupService::createBackup(new $model());
        }

        return redirect()->back()->with('succeses', "model_name".' backup created successfully.');
    }
}
