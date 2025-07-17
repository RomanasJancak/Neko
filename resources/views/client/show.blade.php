@extends('layouts.app')

@section('content')
<div class="container">
    @include('client.partials.info-window', ['client' => $client])
</div>
@endsection
@push('scripts')
    @vite('resources/js/client/show.js')
@endpush