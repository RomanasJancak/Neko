@extends('layouts.app')
@section('style')
<style>
.container-content{
    /* border-style: double; */
}
.no-padding {
    padding: 0 !important;
}
.info-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

.info-icon .tooltip {
            visibility: hidden;
            width: 200px;
            background-color: #f9f9f9;
            color: #333;
            text-align: left;
            border-radius: 5px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%; /* Position the tooltip above the icon */
            left: 50%;
            margin-left: -100px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s;
        }

.info-icon:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }

.info-icon .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #f9f9f9 transparent transparent transparent;
        }

.info-content {
            display: none;
            margin-top: 10px;
        }

.info-content.active {
            display: block;
        }
.input-container {
    position: relative;
    width: 100%;}
.input-container input {
    width: 100%;
    padding: 10px 10px 10px 1.5rem; 
    box-sizing: border-box;}

.input-container .fa-magnifying-glass {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    color: #aaa; 
    }
/* Apply to every element when focused */
/* Base: all elements just have normal border */
/* Base focus setup */
*:focus {
  outline: none;
  position: relative;
  border: 2px solid cyan; 
  border-radius: 8px;
}

/* Animated border highlight */
*:focus::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  padding: 2px; /* border thickness */
  background: linear-gradient(
    90deg,
    rgba(167, 211, 106, 1) 0%,
    rgba(111, 87, 199, 1) 20%,
    rgba(167, 211, 106, 1) 40%,
    rgba(167, 211, 106, 0) 100%
  );
  background-size: 200% 100%;
  background-repeat: repeat;
  pointer-events: none;
  -webkit-mask:
    linear-gradient(#000 0 0) content-box,
    linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;

  animation: slideBorder 3s linear infinite;
}

/* Slide animation */
@keyframes slideBorder {
  0% { background-position: 0 0; }
  100% { background-position: 200% 0; }
}


</style>
@endsection
@section('content')
<div class="container container-content">

  <!-- Top Pagination -->
  <div class="d-flex justify-content-center mt-3 links-pagination"></div>

  <!-- Filter + Sort Header -->
  <div class="row g-3 mb-3">
    <!-- ID -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Id</span>
        <button id="button-sort-id" class="btn btn-sm sort-btn" data-sort-field="id" data-sort-order="asc">
          <i id="button-sort-id-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-id" class="form-control" placeholder="Search...">
      </div>
    </div>

    <!-- Name -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Name</span>
        <button id="button-sort-name" class="btn btn-sm sort-btn" data-sort-field="name" data-sort-order="asc">
          <i id="button-sort-name-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-name" class="form-control" placeholder="Search...">
      </div>
    </div>

    <!-- Client -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Client</span>
        <button id="button-sort-clientName" class="btn btn-sm sort-btn" data-sort-field="clientName" data-sort-order="asc">
          <i id="button-sort-clientName-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-clientName" class="form-control" placeholder="Search...">
      </div>
    </div>
  </div>

  <!-- Data Grid -->
  <div class="row g-3" id="itemListGrid">
  </div>

  <!-- Create Template Button -->
  <div class="text-end my-3">
    <button type="button" class="btn btn-success create-btn">
      <i class="fa fa-plus-circle me-1"></i>Create Template
    </button>
  </div>

  <!-- Bottom Pagination -->
  <div class="d-flex justify-content-center mt-3 links-pagination"></div>
</div>

<!-- Modal task end -->
@endsection
@push('scripts')
@vite('resources/js/jobtemplate/index.js')
@endpush