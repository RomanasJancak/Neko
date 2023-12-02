@extends('layouts.app') <!-- If you have a layout, otherwise skip this line -->

@section('content')
    <div class="row">
        <!-- Your calendar content here -->
        <!-- Use Bootstrap grid and classes to structure your calendar -->
        @foreach($days->chunk(7) as $week)
            <div class="col-md-12">
                <div class="row">
                    @foreach($week as $day)
                        <div class="col-md-1">
                            <div class="card">
                                <div class="card-body">
                                    {{ $day->date }}
                                    <!-- Display other day details if needed -->
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $days->links() }}
    </div>
@endsection