<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Job;
use App\Models\Package;
use App\Models\PackageType;
use App\Models\Pickuptask;
use App\Models\ReturnTask;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusId = Status::query()->value('id')
            ?? Status::create(['name' => 'pending'])->id;

        $clientId = Client::query()->value('id')
            ?? Client::create(['name' => fake()->company(), 'address' => 'Default Address'])->id;

        $managerId = User::query()->value('id')
            ?? User::factory()->create(['password' => 'password'])->id;

        return [
            'eilesNumeris' => fake()->numberBetween(1, 9999),
            'courrier_id' => null,
            'status_id' => $statusId,
            'clientToBill_id' => $clientId,
            'manager_id' => $managerId,
            'date' => now()->toDateString(),
            'price_adjustment_number' => 0,
        ];
    }

    public function withPickupAndDropoff(): self
    {
        return $this->afterCreating(function (Job $job) {
            $this->createRouteForJob($job, 1, false);
        });
    }

    public function withPickupAndMultipleDropoffs(int $min = 2, int $max = 5): self
    {
        return $this->afterCreating(function (Job $job) use ($min, $max) {
            $dropoffCount = random_int(max(2, $min), max($max, $min));
            $this->createRouteForJob($job, $dropoffCount, false);
        });
    }

    public function withPickupDropoffsAndReturn(int $minDropoffs = 1, int $maxDropoffs = 5): self
    {
        return $this->afterCreating(function (Job $job) use ($minDropoffs, $maxDropoffs) {
            $dropoffCount = random_int(max(1, $minDropoffs), max($maxDropoffs, $minDropoffs));
            $this->createRouteForJob($job, $dropoffCount, true);
        });
    }

    private function createRouteForJob(Job $job, int $dropoffCount, bool $withReturn): void
    {
        $statusId = $job->status_id;
        $date = (string) $job->date;

        $pickupTask = Task::create([
            'date' => $date . ' 09:00:00',
            'status_id' => $statusId,
            'job_id' => $job->id,
            'order_number' => 1,
        ]);

        Pickuptask::create([
            'task_id' => $pickupTask->id,
            'status_id' => $statusId,
            'pickup_time_begin' => $date . ' 09:00:00',
            'pickup_time_end' => $date . ' 09:30:00',
            'pickupclientname' => 'Pickup ' . $job->id,
            'pickupclientaddressline' => fake()->streetAddress(),
            'pickupclientcity' => fake()->city(),
            'pickupclientcountry' => fake()->country(),
            'pickupclientpostalcode' => fake()->postcode(),
        ]);

        $packageType = $this->ensurePackageType();

        for ($i = 1; $i <= $dropoffCount; $i++) {
            $dropoffTask = Task::create([
                'date' => $date . ' 10:00:00',
                'status_id' => $statusId,
                'job_id' => $job->id,
                'order_number' => 1 + $i,
            ]);

            Package::create([
                'job_id' => $job->id,
                'task_id' => $dropoffTask->id,
                'status_id' => $statusId,
                'packageType_id' => $packageType->id,
                'orderNumber' => $i,
                'weight' => (string) fake()->numberBetween(1, 20),
                'dimensions' => '30x20x10',
                'quantity' => (string) fake()->numberBetween(1, 3),
                'dropoff_adress_line' => fake()->streetAddress(),
                'dropoff_postal_code' => fake()->postcode(),
                'dropoff_city' => fake()->city(),
                'dropoff_country' => fake()->country(),
                'dropoff_name' => 'Dropoff ' . $i,
                'packagedropofftimebegin' => $date . ' 10:00:00',
                'packagedropofftimeend' => $date . ' 11:00:00',
                'name' => $packageType->name,
                'price' => $packageType->price,
                'baseQuantityThreshold' => $packageType->baseQuantityThreshold,
                'maxQuantityThreshold' => $packageType->maxQuantityThreshold,
            ]);
        }

        if ($withReturn) {
            $returnTask = Task::create([
                'date' => $date . ' 16:00:00',
                'status_id' => $statusId,
                'job_id' => $job->id,
                'order_number' => 2 + $dropoffCount,
            ]);

            ReturnTask::create([
                'task_id' => $returnTask->id,
                'status_id' => $statusId,
                'name' => 'Return ' . $job->id,
                'adress_line' => fake()->streetAddress(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'country' => fake()->country(),
                'time_begin' => $date . ' 16:00:00',
                'time_end' => $date . ' 17:00:00',
            ]);
        }
    }

    private function ensurePackageType(): PackageType
    {
        $existing = PackageType::query()->first();
        if ($existing) {
            return $existing;
        }

        return PackageType::create([
            'name' => 'Parcel',
            'price' => 1000,
            'baseQuantityThreshold' => 1,
            'maxQuantityThreshold' => 10,
            'is_fixed_price' => false,
        ]);
    }
}
