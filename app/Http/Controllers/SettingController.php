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

    public function sqlDumpInterface(DatabaseSqlBackup $backup)
    {
        $tables = $backup->getSelectableTables();
        $selectedTables = collect($tables)
            ->reject(static fn (array $table): bool => $table['restricted'])
            ->pluck('name')
            ->all();

        return view('setting.sql_dump', [
            'files' => $backup->listDumpFiles(),
            'tables' => $tables,
            'selectedTables' => old('selected_tables', $selectedTables),
            'includeDataTables' => old('include_data_tables', $selectedTables),
        ]);
    }

    public function createSqlRestoreDump(Request $request, DatabaseSqlBackup $backup)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:190',
            'chunk_size_kb' => 'nullable|integer|min:1|max:10240',
            'selected_tables' => 'nullable|array',
            'selected_tables.*' => 'string',
            'include_data_tables' => 'nullable|array',
            'include_data_tables.*' => 'string',
        ]);

        $name = $validated['name'] ?? null;
        $chunkSizeKb = (int) ($validated['chunk_size_kb'] ?? 1024);
        $selectedTables = array_values($validated['selected_tables'] ?? []);
        $includeDataTables = array_values($validated['include_data_tables'] ?? []);

        $tableOptions = [];
        foreach ($selectedTables as $tableName) {
            $tableOptions[$tableName] = in_array($tableName, $includeDataTables, true);
        }

        try {
            $result = $backup->createRestoreDump($name, $chunkSizeKb * 1024, $tableOptions);

            $files = array_map(static function (string $path): string {
                return str_replace(storage_path('app') . '/', '', $path);
            }, $result['files']);

            if (!$request->expectsJson()) {
                return redirect()
                    ->route('setting.sqlDump')
                    ->with('success', 'SQL restore dump created successfully.')
                    ->with('generated_files', $files);
            }

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

    public function downloadSqlDump(string $fileName, DatabaseSqlBackup $backup)
    {
        $path = $backup->resolveDumpPath($fileName);

        if ($path === null) {
            abort(404, 'SQL dump file not found.');
        }

        if ($backup->dumpContainsRestrictedUsersTable($path)) {
            abort(403, 'Downloading SQL dump with `users` table is blocked.');
        }

        return response()->download($path, basename($path));
    }

    public function uploadSqlDump(Request $request, DatabaseSqlBackup $backup)
    {
        $validated = $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:51200',
        ]);

        $file = $validated['sql_file'];
        $directory = $backup->getDumpDirectory() . '/uploads';
        File::ensureDirectoryExists($directory);

        $storedPath = $directory . '/upload_' . now()->format('Y_m_d_His') . '_' . $file->getClientOriginalName();
        $file->move($directory, basename($storedPath));

        try {
            $result = $backup->restoreFromSqlFile($storedPath);

            return redirect()
                ->route('setting.sqlDump')
                ->with('success', 'SQL restore completed successfully. Executed statements: ' . $result['executed_statements']);
        } catch (\Throwable $e) {
            return redirect()
                ->route('setting.sqlDump')
                ->with('error', 'Failed to restore SQL dump: ' . $e->getMessage());
        }
    }
}
