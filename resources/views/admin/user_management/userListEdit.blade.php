
@extends('layouts/contentNavbarLayout')

@section('title', 'User - List')

@section('content')

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        <div class="card">
          <!-- Card Header -->
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Edit User</h5>
            <a href="{{ route('admin.userlist') }}" class="btn btn-sm btn-primary">
              <i class="menu-icon tf-icons ri-arrow-left-line"></i></i> Back
            </a>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <form action="{{ route('admin.userlist.update', $data->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('POST')

              <div class="form-group mb-3">
                <label for="name">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Enter full name"
                  value="{{ old('name', $data->name) }}">
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group mb-3">
                <label for="user_name">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Enter username"
                  value="{{ old('user_name', $data->user_name) }}">
                @error('user_name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group mb-3">
                <label for="user_type">User Type <span class="text-danger">*</span></label>
                <select class="form-control" name="user_type" id="user_type">
                  <option value="">-- Select User Type --</option>
                  {{-- <option value="admin" {{ old('user_type', $data->user_type) == 'admin' ? 'selected' : '' }}>Admin</option> --}}
                  <option value="Teacher" {{ old('user_type', $data->user_type) == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                  <option value="Employee" {{ old('user_type', $data->user_type) == 'Employee' ? 'selected' : '' }}>Employee</option>
                </select>
                @error('user_type') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="form-group mb-3">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email"
                  value="{{ old('email', $data->email) }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <input type="hidden" name="id" value="{{ $data->id }}">

              <button type="submit" class="btn btn-primary">Update</button>
            </form>
          </div>
          <!-- End Card Body -->

        </div>

      </div>
    </div>
  </div>
</section>

@endsection
@section('scripts')

@endsection