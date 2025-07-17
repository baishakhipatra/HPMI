@extends('layouts/blankLayout')

@section('title', 'Login Basic - Pages')

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss',
  'resources/css/app.css'
])
@endsection

@section('content')
<div class="position-relative">
  <div class="login-frame">

  <div class="row min-vh-100">
    <div class="col-lg-5 p-0">
      <div class="authentication-inner">

        <div class="login-form-stack">
          <!-- Logo -->
          <div class="app-brand justify-content-center mt-5">
            <a href="{{url('/')}}" class="app-brand-link gap-3">
              <!-- <span class="app-brand-logo demo">@include('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'])</span>
              <span class="app-brand-text demo text-heading fw-semibold">{{config('variables.templateName')}}</span> -->
              <img class="logo" src="{{asset('assets/img/logo-color.png')}}">
            </a>
          </div>
          <!-- /Logo -->

          <div class="card-body mt-1">
            <h4 class="mb-1" style="font-family: 'Georgia', serif; font-size: 18px; text-align: center;">
              <strong>Welcome to {{config('variables.titleName')}}</strong>
            </h4>
            <p class="mb-5"></p>

            <form id="formAuthentication" class="mb-5" action="{{ route('admin.login.submit') }}" method="POST">
              @csrf
              <div class="form-floating form-floating-outline mb-5">
                <input type="text" class="form-control" id="email" name="email-username" placeholder="Enter your email or username" autofocus>
                <label for="email">Email</label>
                  @error('email-username')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
              </div>
              
              <div class="mb-5">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                      <label for="password">Password</label>
                      @error('password')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <span class="input-group-text cursor-pointer"><i class="fa-solid fa-eye"></i></span>
                  </div>
                </div>
              </div>
              <div class="mb-5 pb-2 d-flex justify-content-between pt-2 align-items-center">
                {{-- <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" id="remember-me">
                  <label class="form-check-label" for="remember-me">
                    Remember Me
                  </label>
                </div> --}}
                <a href="{{ route('auth-reset-password-basic') }}" class="float-end mb-1">
                  <span>Forgot Password?</span>
                </a>
              </div>
              <div class="mb-5">
                <button class="btn btn-primary d-grid w-100" type="submit">login</button>
              </div>           
            </form>

            {{-- <p class="text-center mb-5">
              <span>New on HPMI?</span>
              <a href="{{route('auth-register-basic')}}">
                <span>Create an account</span>
              </a>
            </p> --}}
          </div>
        </div>
        <!-- /Login -->
        <!-- <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block">
        <img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}" class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg">
        <img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block">-->
      </div>
    </div>
    <div class="col-md-7 p-0">
     <div class="right-image" style="background:url({{ asset('assets/img/login-image.jpg') }}); background-size:cover; background-repeat:no-repeat; background-position:center;"></div>
    </div>

  </div>

  </div>
</div>
@endsection
