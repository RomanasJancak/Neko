<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Task;
use App\Models\Pickuptask;
use App\Models\Package;
use App\Models\ReturnTask;
use App\Models\CustomTask;

class MigrateTasksToPolymorphic extends Command
{

    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature ='tasks:migrate-morph';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates taskable_type and taskable_id based on old relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      
        // Chunk results so we don't run out of PHP memory
        Task::with(['pickup', 'package', 'return', 'customTask'])
            ->whereNull('taskable_type')
            ->chunk(200, function ($tasks) {
              
                foreach ($tasks as $task) {
                    $type = null;
                    $id = null;
                    if ($task->pickup) {
                        $type = Pickuptask::class;
                        $id = $task->pickup->id;
                    } elseif ($task->package) {
                        $type = Package::class;
                        $id = $task->package->id;
                    } elseif ($task->return) {
                        $type = ReturnTask::class;
                        $id = $task->return->id;
                    } elseif ($task->customTask) {
                        $type = CustomTask::class;
                        $id = $task->customTask->id;
                    }

                    if ($type && $id) {
                        $task->update([
                            'taskable_type' => $type,
                            'taskable_id' => $id,
                        ]);
                    }
                }
                $this->info("Chunk of 200 tasks processed...");
            });

        $this->info('Polymorphic data migration completed successfully!');
    }
}
