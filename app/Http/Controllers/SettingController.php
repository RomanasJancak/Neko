<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\DatabaseSqlBackup;
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
        BackupService::createBackup();
        // $models = $this->getAllModels();
        // foreach ($models as $model) {
            
        // }

        return redirect()->back()->with('succeses', "model_name".' backup created successfully.');
    }

    public function createSqlRestoreDump(Request $request, DatabaseSqlBackup $backup)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:190',
            'chunk_size_kb' => 'nullable|integer|min:1|max:10240',
        ]);

        $name = $validated['name'] ?? null;
        $chunkSizeKb = (int) ($validated['chunk_size_kb'] ?? 1024);

        try {
            $result = $backup->createRestoreDump($name, $chunkSizeKb * 1024);

            $files = array_map(static function (string $path): string {
                return str_replace(storage_path('app') . '/', '', $path);
            }, $result['files']);

            return response()->json([
                'success' => true,
                'message' => 'SQL restore dump created successfully.',
                'chunk_size_kb' => $chunkSizeKb,
                'total_bytes' => $result['total_bytes'],
                'files' => $files,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SQL restore dump.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
