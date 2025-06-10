<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@extends('layouts/contentNavbarLayout')

@section('title', 'Register Basic - Pages')

@section('page-style')
@vite([
'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection


@section('content')

@if(session('success'))
<div class="alert alert-success" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
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

          {{-- Row 1: Name and Username --}}
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" autofocus>
                <label for="name">Full Name</label>
                @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="user_name" name="user_name"
                  placeholder="Enter your username">
                <label for="user_name">Username</label>
                @error('user_name') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
            </div>
          </div>

          {{-- Row 2: Mobile and Email --}}
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number">
                <label for="mobile">Mobile Number</label>
                @error('mobile') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                <label for="email">Email</label>
                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
            </div>
          </div>

          {{-- Row 3: User Type and Password --}}
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="user_type" name="user_type">
                  <option value="Admin" selected>Admin</option>
                  <option value="Teacher">Teacher</option>
                  <option value="Employee">Employee</option>
                </select>
                <label for="user_type">User Type</label>
                @error('user_type') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-password-toggle">
                <div class="input-group input-group-merge">
                  <div class="form-floating form-floating-outline w-50">
                    <input type="password" id="password" class="form-control" name="password" placeholder="********" />
                    <label for="password">Password</label>
                    @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                  </div>
                  <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line ri-20px"></i></span>
                </div>
              </div>
            </div>
          </div>

          {{-- Submit Button --}}
          <button class="btn btn-primary d-grid">
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
    {{-- <img src="{{asset('assets/img/illustrations/tree-3.png')}}" alt="auth-tree"
    class="authentication-image-object-left d-none d-lg-block">
    <img src="{{asset('assets/img/illustrations/auth-basic-mask-light.png')}}"
      class="authentication-image d-none d-lg-block" height="172" alt="triangle-bg"> --}}
    {{-- <img src="{{asset('assets/img/illustrations/tree.png')}}" alt="auth-tree"
    class="authentication-image-object-right d-none d-lg-block"> --}}
  </div>
</div>
{{-- </div> --}}
@endsection
