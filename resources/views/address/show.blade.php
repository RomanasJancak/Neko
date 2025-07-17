
@section('content')
<div class="container">
    @include('address.partials.info-window', ['address' => $address])
</div>
@endsection
@push('scripts')
    @vite('resources/js/address/show.js')
@endpush