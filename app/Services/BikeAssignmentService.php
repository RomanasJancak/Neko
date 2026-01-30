<?php
namespace App\Services;

use App\Models\Bike;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
/**
 * @OA\Schema(
 * schema="BikeAssignmentService",
 * title="Bike Assignment Service",
 * description="Logic for swapping courier bikes"
 * )
 */
class BikeAssignmentService
{
    private User $courier;
    private Bike $newBike;
    private string $day;

    // Zero-argument constructor, or use a static 'make'
    public static function forCourier(User $user): self
    {
        $service = new self();
        $service->courier = $user;
        return $service;
    }

    public function toBike(int $bikeId): self
    {
        $this->newBike = Bike::where('id', $bikeId)
            ->where('status', 'available')
            ->firstOrFail();
        return $this;
    }

    public function onDay(string $day): self
    {
        $this->day = $day;
        return $this;
    }

    /**
     * The core execution method: Zero Arguments.
     */
    public function execute(): void
    {
        $this->ensureUserIsEligible();

        DB::transaction(fn() => $this->performSwap());
    }

    private function ensureUserIsEligible(): void
    {
        // Check if user has a workload linked to the specific Day model/date
        $hasWorkload = $this->courier->workloads()
            ->whereHas('day', function($query) {
                $query->where('date', $this->day);
            })
            ->where('capacity', '>', 0)
            ->exists();

        if (!$this->courier->hasRole('courier') || !$hasWorkload) {
            throw new Exception("Courier eligibility check failed for {$this->day}.");
        }
    }

    private function performSwap(): void
    {
        $this->releasePreviousBike();
        $this->occupyNewBike();
    }

    private function releasePreviousBike(): void
    {
        if ($this->courier->current_bike_id) {
            Bike::where('id', $this->courier->current_bike_id)
                ->update(['status' => 'available']);
        }
    }

    private function occupyNewBike(): void
    {
        $this->newBike->update(['status' => 'occupied']);
        $this->courier->update(['current_bike_id' => $this->newBike->id]);
    }
}