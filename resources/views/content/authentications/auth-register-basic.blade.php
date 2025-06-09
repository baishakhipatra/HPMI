@extends('layouts/contentNavbarLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection


@section('content')
{{-- <div class="position-relative"> --}}
  <div class="col-md-12 form-control-validation fv-plugins-icon-container">
    <div class="form-floating form-floating-outline">

      <!-- Register Card -->
      <div class="card p-7">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="mb-0">User Registration</h3>
          {{-- <a href="{{ route('auth-register-basic') }}" class="btn btn-primary btn-sm">+ Add User</a> --}}
          <a href="{{route('admin.userlist')}}" class="btn btn-primary btn-sm" style="position: absolute; right: 50px;">
            <i class="menu-icon tf-icons ri-arrow-left-line"></i>
              Back
          </a>
        </div>

        <!-- /Logo -->
        <div class="card-body mt-1">

          <form id="formAuthentication" class="mb-5" action="{{ route('admin.register.submit') }}" method="POST">
            @csrf
            <div class="form-floating form-floating-outline mb-5">
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" autofocus>
              <label for="name">Name</label>
            </div>
            <div class="form-floating form-floating-outline mb-5">
              <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Enter your username" autofocus>
              <label for="user_name">Username</label>
            </div>
            <div class="form-floating form-floating-outline mb-5">
              <select class="form-select" id="user_type" name="user_type">
                <option value="admin" selected>Admin</option>
                <option value="teacher">Teacher</option>
                <option value="employee">Employee</option>
              </select>
              <label for="user_type">User Type</label>
            </div>
            <div class="form-floating form-floating-outline mb-5">
              <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email">
              <label for="email">Email</label>
            </div>
            <div class="mb-5 form-password-toggle">
              <div class="input-group input-group-merge">
                <div class="form-floating form-floating-outline">
                  <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                  <label for="password">Password</label>
                </div>
                <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line ri-20px"></i></span>
              </div>
            </div>
            <button class="btn btn-primary d-grid mb-5">
              Sign up
            </button>
          </form>

          {{-- <p class="text-center mb-5">
            <span>Already have an account?</span>
            <a href="{{route('auth-login-basic')}}">
              <span>Sign in instead</span>
            </a>
          </p> --}}
        </div>
      </div>
      <!-- Register Card -->
      {{-- <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block">
      <img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}" class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg"> --}}
      {{-- <img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block"> --}}
    </div>
  </div>
{{-- </div> --}}
@endsection
