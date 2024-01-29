@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
                <h1>Add On Templates</h1>
        </div>
    </div>
    @foreach($addOnRules as $addOnRule)
    <div class="row border border-dark">
        <div class="col-md-4">
            <div class="row"><strong>{{$addOnRule->begin_date}} - {{$addOnRule->end_date}}</strong></div>
        </div>
        <div class="col-md-1">
            <div class="row">Base price</div>
            <div class="row">{{$addOnRule->baseprice}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->distancerule_1_name}}</div>
            <div class="row">{{$addOnRule->distancerule_1_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->distancerule_2_name}}</div>
            <div class="row">{{$addOnRule->distancerule_2_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->extradistancerule_name}}</div>
            <div class="row">{{$addOnRule->extradistancerule_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_1_name}}</div>
            <div class="row">{{$addOnRule->rule_1_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_2_name}}</div>
            <div class="row">{{$addOnRule->rule_2_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_3_name}}</div>
            <div class="row">{{$addOnRule->rule_3_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_4_name}}</div>
            <div class="row">{{$addOnRule->rule_4_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_5_name}}</div>
            <div class="row">{{$addOnRule->rule_5_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_6_name}}</div>
            <div class="row">{{$addOnRule->rule_6_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_7_name}}</div>
            <div class="row">{{$addOnRule->rule_7_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_8_name}}</div>
            <div class="row">{{$addOnRule->rule_8_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_9_name}}</div>
            <div class="row">{{$addOnRule->rule_9_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_10_name}}</div>
            <div class="row">{{$addOnRule->rule_10_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_11_name}}</div>
            <div class="row">{{$addOnRule->rule_11_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_12_name}}</div>
            <div class="row">{{$addOnRule->rule_12_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_13_name}}</div>
            <div class="row">{{$addOnRule->rule_13_value}}</div>
        </div>
        <div class="col-md-1">
            <div class="row">{{$addOnRule->rule_14_name}}</div>
            <div class="row">{{$addOnRule->rule_14_value}}</div>
        </div>
    </div>
    @endforeach
    <div class="row">
        <div class="d-flex justify-content-center mt-3">
            {!! $addOnRules->links() !!}
        </div>
    </div>
</div>
@endsection