
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
            <h5 class="mb-0">Edit Student</h5>
            <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-primary">
              <i class="menu-icon tf-icons ri-arrow-left-line"></i></i> Back
            </a>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <form action="{{ route('admin.studentupdate', $student->id) }}" method="POST">
              @csrf
              @method('POST')

              {{-- <div class="col-md-6">
                <label for="student_name">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="student_name" id="student_name" placeholder="Enter full name"
                  value="{{ old('student_name', $student->student_name) }}">
                @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              <div class="col-md-6">
                <label for="date_of_birth">Date Of Birth<span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="Enter full name"
                  value="{{ old('date_of_birth', $student->date_of_birth) }}">
                @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
              </div> --}}
              <div class="row g-3">
                <!-- Student Name -->
                <div class="col-md-6">
                    <label for="student_name" class="form-label">Student Name*</label>
                    <input type="text" class="form-control" 
                            id="student_name" name="student_name" 
                            value="{{ old('student_name', $student->student_name) }}" required>
                    @error('student_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Date of Birth -->
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth*</label>
                    <input type="date" class="form-control" 
                            id="date_of_birth" name="date_of_birth" 
                            value="{{ old('date_of_birth', $student->date_of_birth) }}" required>
                    @error('date_of_birth')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Gender -->
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender*</label>
                    <select class="form-select" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Parent Name -->
                <div class="col-md-8">
                    <label for="parent_name" class="form-label">Parent/Guardian Name*</label>
                    <input type="text" class="form-control" 
                            id="parent_name" name="parent_name" 
                            value="{{ old('parent_name', $student->parent_name) }}" required>
                    @error('parent_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" 
                            id="email" name="email" 
                            value="{{ old('email', $student->email) }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" 
                            id="phone_number" name="phone_number" 
                            value="{{ old('phone_number', $student->phone_number) }}">
                    @error('phone_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" 
                                id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Admission Date -->
                <div class="col-md-4">
                    <label for="admission_date" class="form-label">Admission Date*</label>
                    <input type="date" class="form-control" 
                            id="admission_date" name="admission_date" 
                            value="{{ old('admission_date', $student->admission_date) }}" required>
                    @error('admission_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Class -->
                <div class="col-md-4">
                    <label for="class" class="form-label">Class*</label>
                    <input type="text" class="form-control" 
                            id="class" name="class" 
                            value="{{ old('class', $student->class) }}" required>
                    @error('class')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Section -->
                <div class="col-md-4">
                    <label for="section" class="form-label">Section*</label>
                    <input type="text" class="form-control" 
                            id="section" name="section" 
                            value="{{ old('section', $student->section) }}" required>
                    @error('section')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Roll Number -->
                <div class="col-md-4">
                    <label for="roll_number" class="form-label">Roll Number*</label>
                    <input type="number" class="form-control" 
                            id="roll_number" name="roll_number" 
                            value="{{ old('roll_number', $student->roll_number) }}" required>
                    @error('roll_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


              <input type="hidden" name="id" value="{{ $student->id }}">

              <button type="submit" class="btn btn-primary">Update</button>
            </form>
          </div>

        </div>

      </div>
    </div>
  </div>
</section>

@endsection
@section('scripts')

@endsection