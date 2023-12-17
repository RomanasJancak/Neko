@extends('layouts.app')

@section('content')
<div class="container mt-5">
    
@if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    <div class="row">
        <div class="col-md-12">
            <h1>Create Add On Rule</h1>
            <form action="{{ route('addonrule.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="begin_date">Timeframe:</label>
                            <input type="date" id="begin_date" name="begin_date" value="{{ old('begin_date', isset($lastCreated) ? \Carbon\Carbon::parse($lastCreated->begin_date)->format('Y-m-d') : '') }}">
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', isset($lastCreated) ? \Carbon\Carbon::parse($lastCreated->end_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="baseprice">Base price</label>
                            <input type="text" class="form-control" id="baseprice" name="baseprice" value="{{ old('baseprice', isset($lastCreated) ? $lastCreated->baseprice : '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="distancerule_1_value">{{$lastCreated->distancerule_1_name}}</label>
                            <input type="text" class="form-control" id="distancerule_1_value" name="distancerule_1_value" value="{{ old('distancerule_1_value', isset($lastCreated) ? $lastCreated->distancerule_1_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="distancerule_2_value">{{$lastCreated->distancerule_2_name}}</label>
                            <input type="text" class="form-control" id="distancerule_2_value" name="distancerule_2_value" value="{{ old('distancerule_2_value', isset($lastCreated) ? $lastCreated->distancerule_2_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="extradistancerule_value">{{$lastCreated->extradistancerule_name}}</label>
                            <input type="text" class="form-control" id="extradistancerule_value" name="extradistancerule_value" value="{{ old('extradistancerule_value', isset($lastCreated) ? $lastCreated->extradistancerule_value : '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_1_value">{{$lastCreated->rule_1_name}}</label>
                            <input type="text" class="form-control" id="rule_1_value" name="rule_1_value" value="{{ old('rule_1_value', isset($lastCreated) ? $lastCreated->rule_1_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_2_value">{{$lastCreated->rule_2_name}}</label>
                            <input type="text" class="form-control" id="rule_2_value" name="rule_2_value" value="{{ old('rule_2_value', isset($lastCreated) ? $lastCreated->rule_2_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_3_value">{{$lastCreated->rule_3_name}}</label>
                            <input type="text" class="form-control" id="rule_3_value" name="rule_3_value" value="{{ old('rule_3_value', isset($lastCreated) ? $lastCreated->rule_3_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_4_value">{{$lastCreated->rule_4_name}}</label>
                            <input type="text" class="form-control" id="rule_4_value" name="rule_4_value" value="{{ old('rule_4_value', isset($lastCreated) ? $lastCreated->rule_4_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_5_value">{{$lastCreated->rule_5_name}}</label>
                            <input type="text" class="form-control" id="rule_5_value" name="rule_5_value" value="{{ old('rule_5_value', isset($lastCreated) ? $lastCreated->rule_5_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_6_value">{{$lastCreated->rule_6_name}}</label>
                            <input type="text" class="form-control" id="rule_6_value" name="rule_6_value" value="{{ old('rule_6_value', isset($lastCreated) ? $lastCreated->rule_6_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_7_value">{{$lastCreated->rule_7_name}}</label>
                            <input type="text" class="form-control" id="rule_7_value" name="rule_7_value" value="{{ old('rule_7_value', isset($lastCreated) ? $lastCreated->rule_7_value : '') }}">
                        </div>
                    </div>  
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_8_value">{{$lastCreated->rule_8_name}}</label>
                            <input type="text" class="form-control" id="rule_8_value" name="rule_8_value" value="{{ old('rule_8_value', isset($lastCreated) ? $lastCreated->rule_8_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_9_value">{{$lastCreated->rule_9_name}}</label>
                            <input type="text" class="form-control" id="rule_9_value" name="rule_9_value" value="{{ old('rule_9_value', isset($lastCreated) ? $lastCreated->rule_9_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_10_value">{{$lastCreated->rule_10_name}}</label>
                            <input type="text" class="form-control" id="rule_10_value" name="rule_10_value" value="{{ old('rule_10_value', isset($lastCreated) ? $lastCreated->rule_10_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_13_value">{{$lastCreated->rule_13_name}}</label>
                            <input type="text" class="form-control" id="rule_13_value" name="rule_13_value" value="{{ old('rule_13_value', isset($lastCreated) ? $lastCreated->rule_13_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="rule_14_value">{{$lastCreated->rule_14_name}}</label>
                            <input type="text" class="form-control" id="rule_14_value" name="rule_14_value" value="{{ old('rule_14_value', isset($lastCreated) ? $lastCreated->rule_14_value : '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="rule_11_value">{{$lastCreated->rule_11_name}}</label>
                            <input type="text" class="form-control" id="rule_11_value" name="rule_11_value" value="{{ old('rule_11_value', isset($lastCreated) ? $lastCreated->rule_11_value : '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="rule_12_value">{{$lastCreated->rule_12_name}}</label>
                            <input type="text" class="form-control" id="rule_12_value" name="rule_12_value" value="{{ old('rule_12_value', isset($lastCreated) ? $lastCreated->rule_12_value : '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="hidden" name="distancerule_1_name" id="distancerule_1_name" value="{{$lastCreated->distancerule_1_name}}"><input type="hidden" name="distancerule_2_name" id="distancerule_2_name" value="{{$lastCreated->distancerule_2_name}}"><input type="hidden" name="extradistancerule_name" id="extradistancerule_name" value="{{$lastCreated->extradistancerule_name}}">
                        <input type="hidden" name="rule_1_name" id="rule_1_name" value="{{$lastCreated->rule_1_name}}">
                        <input type="hidden" name="rule_2_name" id="rule_2_name" value="{{ $lastCreated->rule_2_name }}">
                        <input type="hidden" name="rule_3_name" id="rule_3_name" value="{{ $lastCreated->rule_3_name }}">
                        <input type="hidden" name="rule_4_name" id="rule_4_name" value="{{ $lastCreated->rule_4_name }}">
                        <input type="hidden" name="rule_5_name" id="rule_5_name" value="{{ $lastCreated->rule_5_name }}">
                        <input type="hidden" name="rule_6_name" id="rule_6_name" value="{{ $lastCreated->rule_6_name }}">
                        <input type="hidden" name="rule_7_name" id="rule_7_name" value="{{ $lastCreated->rule_7_name }}">
                        <input type="hidden" name="rule_8_name" id="rule_8_name" value="{{ $lastCreated->rule_8_name }}">
                        <input type="hidden" name="rule_9_name" id="rule_9_name" value="{{ $lastCreated->rule_9_name }}">
                        <input type="hidden" name="rule_10_name" id="rule_10_name" value="{{ $lastCreated->rule_10_name }}">
                        <input type="hidden" name="rule_11_name" id="rule_11_name" value="{{ $lastCreated->rule_11_name }}">
                        <input type="hidden" name="rule_12_name" id="rule_12_name" value="{{ $lastCreated->rule_12_name }}">
                        <input type="hidden" name="rule_13_name" id="rule_13_name" value="{{ $lastCreated->rule_13_name }}">
                        <input type="hidden" name="rule_14_name" id="rule_14_name" value="{{ $lastCreated->rule_14_name }}">

                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">Create Rule</button>
            </form>
        </div>
    </div>
</div>
@endsection
