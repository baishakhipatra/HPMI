
@extends('layouts/contentNavbarLayout')

@section('title', 'Edit - Teacher')

@section('content')

<section class="content">
    @section('content')
        <div class="card p-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Edit Teacher</h3>
            <a href="{{ route('admin.teacherlist') }}" class="btn btn-sm btn-primary">
                <i class="ri-arrow-left-line"></i> Back
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.teacherlist.update', $data->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('POST')

            {{-- Row 1: Teacher ID & Name --}}
            <div class="row mb-3">
                <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <input type="text" name="teacher_id" class="form-control" placeholder="Teacher ID" value="{{ old('teacher_id', $data->teacher_id) }}">
                    <label>Teacher ID</label>
                    @error('teacher_id') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name', $data->name) }}">
                    <label>Full Name</label>
                    @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
            </div>

            {{-- Row 2: Email, Phone, DOB --}}
            <div class="row mb-3">
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email', $data->email) }}">
                    <label>Email</label>
                    @error('email') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ old('phone', $data->phone) }}">
                    <label>Phone</label>
                    @error('phone') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $data->date_of_birth) }}">
                    <label>Date of Birth</label>
                    @error('date_of_birth') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
            </div>

            {{-- Row 3: DOJ, Qualifications, Subjects Taught --}}
            <div class="row mb-3">
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <input type="date" name="date_of_joining" class="form-control" value="{{ old('date_of_joining', $data->date_of_joining) }}">
                    <label>Date of Joining</label>
                    @error('date_of_joining') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <textarea name="qualifications" class="form-control" placeholder="Qualifications">{{ old('qualifications', $data->qualifications) }}</textarea>
                    <label>Qualifications</label>
                    @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <textarea name="subjects_taught" class="form-control" placeholder="Subjects Taught">{{ old('subjects_taught', $data->subjects_taught) }}</textarea>
                    <label>Subjects Taught</label>
                    @error('subjects_taught') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
            </div>

            {{-- Row 4: Classes Assigned, Role --}}
            <div class="row mb-4">
                <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <textarea name="classes_assigned" class="form-control" placeholder="Classes Assigned">{{ old('classes_assigned', $data->classes_assigned) }}</textarea>
                    <label>Classes Assigned</label>
                    @error('classes_assigned') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
                <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <select name="role" class="form-select">
                    <option value="Teacher" {{ old('role', $data->role) == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="Admin" {{ old('role', $data->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Employee" {{ old('role', $data->role) == 'Employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                    <label>Role</label>
                    @error('role') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
            </div>

            {{-- Row 5: User Type --}}
            <div class="row mb-4">
                <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <select name="user_type" class="form-select">
                    <option value="Teacher" {{ old('user_type', $data->user_type) == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="Employee" {{ old('user_type', $data->user_type) == 'Employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                    <label>User Type</label>
                    @error('user_type') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                </div>
            </div>

            {{-- Hidden ID --}}
            <input type="hidden" name="id" value="{{ $data->id }}">

            <button type="submit" class="btn btn-primary d-block">
                Update
            </button>
            </form>
        </div>
        </div>
    @endsection
</section>

@endsection
@section('scripts')

@endsection