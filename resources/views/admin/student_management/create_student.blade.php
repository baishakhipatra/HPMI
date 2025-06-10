<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - List')

@section('content')

@if(session('success'))
    <div class="alert alert-success" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<!-- Basic Bootstrap Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="mb-0">Create Student</h3>
    <a href="{{ route('admin.studentlist') }}" class="btn btn-primary btn-sm">Back</a>
  </div>

  <div class="card-body">
    <div class="table-responsive text-nowrap">
        <form action="{{ route('admin.studentstore') }}" method="POST" class="container mt-4">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="student_name" class="form-label">Full Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="student_name" name="student_name" placeholder="Enter full name">
                    @error('student_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
            
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    @error('date_of_birth')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                    @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="admission_date" class="form-label">Admission Date<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="admission_date" name="admission_date">
                    @error('admission_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="email" class="form-label">Email Address<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="student@example.com">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
               
                <div class="col-md-6">
                    <label for="parent_name" class="form-label">Parent/Guardian Name</label>
                    <input type="text" class="form-control" id="parent_name" name="parent_name" placeholder="Enter parent/guardian name">
                    @error('parent_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Contact Number<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter contact number">
                    @error('phone_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                
                <div class="col-12">
                    <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter full address"></textarea>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
              
                <div class="col-md-4">
                    <label for="class" class="form-label">Class</label>
                    <input type="text" class="form-control" id="class" name="class" placeholder="Enter class">
                    @error('class')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" class="form-control" id="section" name="section" placeholder="Enter section">
                    @error('section')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="roll_number" class="form-label">Roll Number<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="roll_number" name="roll_number" placeholder="Enter roll number">
                    @error('roll_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-user-plus me-2"></i> Add Student
                    </button>
                </div>
            </div>
        </form>  
    </div>
  </div>
  
</div>
@endsection