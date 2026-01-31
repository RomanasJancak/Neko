@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="max-w-5xl mx-auto p-6 bg-gray-900 shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-6 text-gray-100">User Day Statuses</h2>

        {{-- Search by day (date) --}}
        <form method="GET" class="mb-6 flex gap-4 items-end">
            <div>
                <label class="block text-gray-300 text-sm mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $start }}" class="bg-gray-800 text-gray-100 border-gray-700 rounded-md">
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $end }}" class="bg-gray-800 text-gray-100 border-gray-700 rounded-md">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Search</button>
            <a href="{{ route('user-day-statuses.index') }}" class="text-gray-400 hover:text-gray-200 ml-2">Reset</a>
        </form>

        {{-- Create new --}}
        <form action="{{ route('user-day-statuses.store') }}" method="POST" class="mb-8 flex gap-4 flex-wrap items-end">
            @csrf
            <div>
                <label class="block text-gray-300 text-sm mb-1">User</label>
                <select name="user_id" required class="bg-gray-800 text-gray-100 border-gray-700 rounded-md">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Status</label>
                <select name="user_status_id" required class="bg-gray-800 text-gray-100 border-gray-700 rounded-md">
                    <option value="">Select Status</option>
                    @foreach($userStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status->name ?? $status->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Day</label>
                <select name="day_id" required class="bg-gray-800 text-gray-100 border-gray-700 rounded-md">
                    <option value="">Select Day</option>
                    @foreach($days as $day)
                        <option value="{{ $day->id }}">{{ $day->date }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Add</button>
        </form>

        {{-- Edit form (inline modal style) --}}
        @if($editStatus)
        <form action="{{ route('user-day-statuses.update', $editStatus) }}" method="POST" class="mb-8 flex gap-4 flex-wrap items-end bg-gray-800 p-4 rounded">
            @csrf
            @method('PUT')
            <input type="hidden" name="edit_id" value="{{ $editStatus->id }}">
            <div>
                <label class="block text-gray-300 text-sm mb-1">User</label>
                <select name="user_id" required class="bg-gray-700 text-gray-100 border-gray-600 rounded-md">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($editStatus->user_id == $user->id) selected @endif>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Status</label>
                <select name="user_status_id" required class="bg-gray-700 text-gray-100 border-gray-600 rounded-md">
                    @foreach($userStatuses as $status)
                        <option value="{{ $status->id }}" @if($editStatus->user_status_id == $status->id) selected @endif>
                            {{ $status->status->name ?? $status->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-300 text-sm mb-1">Day</label>
                <select name="day_id" required class="bg-gray-700 text-gray-100 border-gray-600 rounded-md">
                    @foreach($days as $day)
                        <option value="{{ $day->id }}" @if($editStatus->day_id == $day->id) selected @endif>
                            {{ $day->date }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Update</button>
            <a href="{{ route('user-day-statuses.index') }}" class="text-gray-400 hover:text-gray-200 ml-2">Cancel</a>
        </form>
        @endif

        {{-- Table --}}
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Day</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-gray-900 divide-y divide-gray-800">
                @foreach($userDayStatuses as $uds)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $uds->user->name ?? $uds->user_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-100">{{ $uds->userStatus->status->name ?? $uds->user_status_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-400">{{ $uds->day->date ?? $uds->day_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right flex gap-2 justify-end">
                        <form action="{{ route('user-day-statuses.index') }}" method="GET" style="display:inline;">
                            <input type="hidden" name="edit_id" value="{{ $uds->id }}">
                            <input type="hidden" name="start_date" value="{{ $start }}">
                            <input type="hidden" name="end_date" value="{{ $end }}">
                            <button type="submit" class="text-indigo-400 hover:text-indigo-600">Edit</button>
                        </form>
                        <form action="{{ route('user-day-statuses.destroy', $uds) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this entry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6">
            {{ $userDayStatuses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection