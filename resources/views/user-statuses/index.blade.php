@extends('layouts.app')
@section('content')
<div class="container mt-5">
  <div class="max-w-4xl mx-auto p-6 bg-gray-900 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-100">Manage User Statuses</h2>

    <form action="{{ route('user-statuses.store') }}" method="POST" class="mb-8 flex gap-4">
        @csrf
        <div class="flex-1">
            <input type="text" name="name" placeholder="New Status Name (e.g. Sick, Vacation)" 
                   class="w-full border-gray-700 bg-gray-800 text-gray-100 rounded-md shadow-sm focus:border-indigo-400 focus:ring-indigo-400" required>
            @error('name') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Create Status
        </button>
    </form>

    <hr class="mb-8 border-gray-700">

    <table class="min-w-full divide-y divide-gray-800">
        <thead class="bg-gray-800">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Created At</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-gray-900 divide-y divide-gray-800">
            @foreach($userStatuses as $us)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-100">
                    {{ $us->status->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-400">
                    {{ $us->created_at->format('Y-m-d') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <form action="{{ route('user-statuses.destroy', $us) }}" method="POST" onsubmit="return confirm('Deleting this will affect historical daily logs. Continue?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
  </div>  
</div>
@endsection