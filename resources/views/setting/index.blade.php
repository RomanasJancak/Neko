@extends('layouts.app')

@section('title', 'User Settings')

@section('style')
<style>
fieldset {
    border: 1px solid #ccc;
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 8px;
}
legend {
    font-weight: bold;
    padding: 0 10px;
}
</style>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="alert alert-info mb-4">
    Personal date format changes how dates are shown in the app for your account. The invoice PDF date format is global and editable by admins only.
</div>

<form method="POST" action="{{ route('setting.update') }}">
    @csrf

    <x-setting.field-group
        :settings="$full"
        :values="$values"
        :fullDefinition="$full"
        prefix=""
    />

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
@endsection
