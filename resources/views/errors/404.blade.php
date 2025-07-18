@extends('layouts/blankLayout')

@section('title', 'Error - Pages')

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection


@section('content')
<!-- Error -->
<div class="misc-wrapper">
  <h1 class="mb-2 mx-2" style="font-size: 6rem;line-height: 6rem;">404</h1>
  <h4 class="mb-2">Page Not Found 🙄</h4>
  <p class="mb-10 mx-2">we couldn't find the page you are looking for</p>
  <div class="d-flex justify-content-center mt-5">
    <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="misc-tree" class="img-fluid misc-object d-none d-lg-inline-block">
    <img src="{{asset('assets/img/illustrations/misc-mask-light.png')}}" alt="misc-error" class="misc-bg d-none d-lg-inline-block z-n1" height="172">
    <div class="d-flex flex-column align-items-center">
      <img src="{{asset('assets/img/illustrations/404.png')}}" alt="misc-error" class="misc-model img-fluid z-1" width="780">
      {{-- <div>
        <a href="{{url('/')}}" class="btn btn-primary text-center my-6">Back to home</a>
      </div> --}}
    </div>
  </div>
</div>
<!-- /Error -->
@endsection



{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found - 404</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            text-align: center;
            padding: 100px;
        }
        h1 {
            font-size: 50px;
            color: #ff6b6b;
        }
        p {
            font-size: 20px;
            color: #333;
        }
        a {
            text-decoration: none;
            background: #3490dc;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

@section('title', 'Error - Pages')

</body>
</html> --}}

