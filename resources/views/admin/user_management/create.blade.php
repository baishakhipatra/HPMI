<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Create - Teacher')

@section('content')

<div class="card p-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3>Create Employee</h3>
    <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-primary">
      <i class="ri-arrow-left-line"></i> Back
    </a>
  </div>

  <div class="card-body">
    <form action="{{ route('admin.employee.store') }}" method="POST">
      @csrf

      {{-- Row 1: Teacher ID & Name --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="teacher_id" class="form-control" placeholder="Teacher ID"
              value="{{ old('teacher_id') }}">
            <label>Teacher ID</label>
            @error('teacher_id') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}">
            <label>Full Name</label>
            @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="user_id" class="form-control" value="{{$user_id}}" readonly>
            <label>Employee ID</label>
            @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- Row 2: Email, Phone, DOB --}}
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
            <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone') }}">
            <label>Phone</label>
            @error('phone') <p class="text-danger small">{{ $message }}</p> @enderror
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

      {{-- Row 3: DOJ, Qualifications, Subjects Taught --}}
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
            <textarea name="qualifications" class="form-control"
              placeholder="Qualifications">{{ old('qualifications') }}</textarea>
            <label>Qualifications</label>
            @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <textarea name="subjects_taught" class="form-control"
              placeholder="Subjects Taught">{{ old('subjects_taught') }}</textarea>
            <label>Subjects Taught</label>
            @error('subjects_taught') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      {{-- Row 4: Classes Assigned, Role --}}
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <textarea name="classes_assigned" class="form-control"
              placeholder="Classes Assigned">{{ old('classes_assigned') }}</textarea>
            <label>Classes Assigned</label>
            @error('classes_assigned') <p class="text-danger small">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary d-block">
        Create
      </button>
    </form>
  </div>
</div>
@endsection
