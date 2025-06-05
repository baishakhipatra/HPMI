@extends('layouts/commonMaster' )

@section('layoutContent')


<!-- Global Validation Errors -->
@if ($errors->any())
  <div class="container mt-3">
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
@endif
<!-- Content -->
@yield('content')
<!--/ Content -->

@endsection
