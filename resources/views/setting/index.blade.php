@extends('layouts.app')

@section('title', 'User Settings')

@section('style')
<style>
/* Add custom styles here if needed */
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('setting.update') }}">
    @csrf

    @foreach($definition as $key => $setting)
        <div class="mb-4">
            <label class="form-label font-semibold">{{ $setting['label'] }}</label>

            @if($setting['type'] === 'select')
                <select name="{{ $key }}" class="form-control">
                    @foreach($setting['options'] as $optionValue => $label)
                        <option value="{{ $optionValue }}" @selected(old($key, $values[$key]) == $optionValue)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            @else
                <input type="{{ $setting['type'] }}" name="{{ $key }}"
                       value="{{ old($key, $values[$key]) }}" class="form-control" />
            @endif

            @error($key)
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
  });
</script>
@endsection
