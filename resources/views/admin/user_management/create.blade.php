
@extends('layouts/contentNavbarLayout')

@section('title', 'Create - Employee')

@section('content')

<div class="card p-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="mb-0 text-primary">Create Employee</h3>
    <a href="{{ route('admin.employee.index') }}" class="btn btn-danger">
      <i class="ri-arrow-left-line"></i> Back
    </a>
  </div>

  <div class="card-body">
    <form action="{{ route('admin.employee.store') }}" method="POST">
      @csrf

      {{-- Row 1: Employee ID, Name, Type --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="user_id" value="{{ old('user_id', $user_id) }}" class="form-control" readonly>
            <label>Employee ID</label>
            @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}">
            <label>Full Name<span class="text-danger">*</span></label>
            @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <select name="designation_id" class="form-select">
              <option value="">Select Designation</option>
              @foreach($designations as $designation)
                <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                  {{ ucwords($designation->name) }}
                </option>
              @endforeach
            </select>
            <label>Designation<span class="text-danger">*</span></label>
            @error('designation_id') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <input type="hidden" name="user_type" value="Employee">
        {{-- <input type="hidden" name="designation_id" value="2"> --}}
      </div>

      {{-- Row 2: Email, Mobile, DOB --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
            <label>Email<span class="text-danger">*</span></label>
            @error('email') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="mobile" class="form-control" placeholder="Mobile" value="{{ old('mobile') }}">
            <label>Phone<span class="text-danger">*</span></label>
            @error('mobile') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
            <label>Date of Birth<span class="text-danger">*</span></label>
            @error('date_of_birth') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- Row 3: DOJ, Qualifications, Address --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining') }}">
            <label>Date of Joining<span class="text-danger">*</span></label>
            @error('date_of_joining') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="qualifications" class="form-control" placeholder="Qualifications" value="{{ old('qualifications') }}">
            <label>Qualifications<span class="text-danger">*</span></label>
            @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <textarea name="address" class="form-control" placeholder="Address" style="height: 70px;">{{ old('address') }}</textarea>
            <label>Address<span class="text-danger">*</span></label>
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
                <label for="password">Password<span class="text-danger">*</span></label>
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
@section('scripts')
@endsection
