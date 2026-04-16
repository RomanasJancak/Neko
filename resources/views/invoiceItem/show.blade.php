@extends('layouts.app')

@section('content')

<div class="container py-5">
    <x-invoice-item-view :item="$invoiceItem" />
</div>
@endsection