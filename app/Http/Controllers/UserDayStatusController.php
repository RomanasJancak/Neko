<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserDayStatusRequest;
use App\Http\Requests\UpdateUserDayStatusRequest;
use App\Models\UserDayStatus;
use App\Models\UserStatus;
use App\Models\User;
use App\Models\Day;
use Illuminate\Http\Request;

class UserDayStatusController extends Controller
{
    public function index(Request $request)
    {
        $days = Day::orderBy('date', 'desc')->get();
        $users = User::orderBy('name')->get();
        $userStatuses = UserStatus::with('status')->get();

        $query = UserDayStatus::with(['user', 'userStatus.status', 'day']);

        // Filter by day range if provided
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        if ($start && $end) {
            $startDay = Day::where('date', '>=', $start)->orderBy('date')->first();
            $endDay = Day::where('date', '<=', $end)->orderBy('date', 'desc')->first();
            if ($startDay && $endDay) {
                $query->whereBetween('day_id', [$startDay->id, $endDay->id]);
            }
        }

        $userDayStatuses = $query->orderByDesc('day_id')->paginate(20);

        // For edit modal
        $editId = $request->input('edit_id');
        $editStatus = $editId ? UserDayStatus::find($editId) : null;

        return view('user-day-statuses.index', compact(
            'userDayStatuses', 'userStatuses', 'users', 'days', 'start', 'end', 'editStatus'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_status_id' => 'required|exists:user_statuses,id',
            'date' => 'required|date',
        ]);

        // Find or create the Day by date
        $day = Day::firstOrCreate(['name' => $validated['date'], 'date' => $validated['date']]);

        UserDayStatus::create([
            'user_id' => $validated['user_id'],
            'user_status_id' => $validated['user_status_id'],
            'day_id' => $day->id,
        ]);

        return redirect()->route('user-day-statuses.index')->with('success', 'Created!');
    }

    public function update(Request $request, UserDayStatus $userDayStatus)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_status_id' => 'required|exists:user_statuses,id',
            'date' => 'required|date',
        ]);
        $userDayStatus->update([
            'user_id' => $validated['user_id'],
            'user_status_id' => $validated['user_status_id'],
            'day_id' => Day::firstOrCreate(['name' => $validated['date'], 'date' => $validated['date']])->id,
        ]);
        return redirect()->route('user-day-statuses.index')->with('success', 'Updated!');
    }

    public function destroy(UserDayStatus $userDayStatus)
    {
        $userDayStatus->delete();
        return redirect()->route('user-day-statuses.index')->with('success', 'Deleted!');
    }
}
