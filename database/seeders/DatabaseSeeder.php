<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Retrieve all models and seeders dynamically
        $seeders = $this->getAllModelSeeders();

        // Call each seeder
        $this->call($seeders);
    }
        /**
     * Get all model seeders dynamically based on model names.
     *
     * @return array
     */
    private function getAllModelSeeders(): array
    {
        $seeders = [];
        $modelsPath = app_path('Models'); // Models directory path
        $namespace = 'Database\\Seeders\\'; // Seeders namespace

        // Scan the Models directory for model files
        $modelFiles = File::allFiles($modelsPath);

        foreach ($modelFiles as $file) {
            // Get the model name
            $modelName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $seederClass = $namespace . $modelName . 'Seeder';

            // Check if the corresponding seeder class exists
            if (class_exists($seederClass)) {
                $seeders[] = $seederClass;
            }
        }

        return $seeders;
    }
}
