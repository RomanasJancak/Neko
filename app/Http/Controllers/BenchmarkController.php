<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class BenchmarkController extends Controller
{
    public function run(Request $request)
    {
        // 1. Resolve your JobController out of the service container
        $jobController = App::make(\App\Http\Controllers\JobController::class);
        
        $methodsToTest = [
            'fetchJobsPaginate',
            'fetchJobsPaginateLight',
            'fetchJobsPaginateExtraLight'
        ];

        $results = [];

        foreach ($methodsToTest as $method) {
            if (!method_exists($jobController, $method)) {
                $results[$method] = ['error' => 'Method not found in JobController'];
                continue;
            }

            // Force a clean garbage collection cycle before each loop pass
            gc_collect_cycles();

            // Record execution and memory baselines
            $startMemory = memory_get_usage();
            $startTime = microtime(true);

            try {
                // 2. Pass explicitly named dependencies inside an associative array context.
                // Laravel's App::call container relies on the parameter type-hints 
                // and explicitly mapped strings to wire up method injections properly.
                $response = App::call([$jobController, $method], [
                    'request' => $request
                ]);

                // Record computational metrics immediately post-execution
                $endTime = microtime(true);
                $endMemory = memory_get_usage();
                $peakMemory = memory_get_peak_usage();

                $executionTimeMs = ($endTime - $startTime) * 1000; 
                $memoryUsedMb = ($endMemory - $startMemory) / 1024 / 1024;
                $peakMemoryMb = $peakMemory / 1024 / 1024;

                $results[$method] = [
                    'execution_time' => number_format($executionTimeMs, 2) . ' ms',
                    'memory_allocated_during_run' => number_format($memoryUsedMb, 4) . ' MB',
                    'peak_system_memory' => number_format($peakMemoryMb, 2) . ' MB',
                    'http_status_returned' => $response->getStatusCode()
                ];

            } catch (\Exception $e) {
                $results[$method] = [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ];
            }
        }

        return response()->json([
            'benchmark_parameters' => $request->all(),
            'results' => $results
        ]);
    }
}