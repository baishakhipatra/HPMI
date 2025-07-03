@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center mt-8">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">My Profile</h4>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                            <i class="menu-icon tf-icons ri-arrow-left-line"></i> Back
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ old('name', $admin->name) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_name">Username</label>
                                <input type="text" id="user_name" name="user_name" class="form-control"
                                    value="{{ old('user_name', $admin->user_name) }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="{{ old('email', $admin->email) }}">
                            </div>

                            <div class="form-group mb-4">
                                <label for="user_type">User Type</label>
                                <input type="text" id="user_type" class="form-control" value="{{ $admin->user_type }}" disabled>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection