<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Create - Employee')

@section('content')

<div class="card p-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3>Create Employee</h3>
    <a href="{{ route('admin.employee.index') }}" class="btn btn-danger">
      <i class="ri-arrow-left-line"></i> Back
    </a>
  </div>

  <div class="card-body">
    <form action="{{ route('admin.employee.store') }}" method="POST">
      @csrf

      {{-- Row 1: Employee ID, Name, Type --}}
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="user_id" value="{{ old('user_id', $user_id) }}" class="form-control" readonly>
            <label>Employee ID</label>
            @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}">
            <label>Full Name</label>
            @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        {{-- <div class="col-md-4">
          <input type="hidden" name="user_type" class="form-control" value="Employee" readonly>
          @error('user_type') <p class="text-danger small">{{ $message }}</p> @enderror
        </div> --}}
        <input type="hidden" name="user_type" value="Employee">
        <input type="hidden" name="designation_id" value="2">
      </div>

      {{-- Row 2: Email, Mobile, DOB --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
            <label>Email</label>
            @error('email') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="mobile" class="form-control" placeholder="Mobile" value="{{ old('mobile') }}">
            <label>Phone</label>
            @error('mobile') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            <label>Date of Birth</label>
            @error('date_of_birth') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- Row 3: DOJ, Qualifications, Address --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining') }}">
            <label>Date of Joining</label>
            @error('date_of_joining') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="qualifications" class="form-control" placeholder="Qualifications" value="{{ old('qualifications') }}">
            <label>Qualifications</label>
            @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <textarea name="address" class="form-control" placeholder="Address" style="height: 70px;">{{ old('address') }}</textarea>
            <label>Address</label>
            @error('address') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- Row 4: Password --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-password-toggle">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline flex-grow-1">
                <input type="password" id="password" class="form-control" name="password" placeholder="********">
                <label for="password">Password</label>
                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
              </div>
              <span class="input-group-text cursor-pointer">
                <i class="ri-eye-off-line ri-20px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary px-4 py-2">Create</button>
      </div>
      
    </form>
  </div>

</div>
@endsection
