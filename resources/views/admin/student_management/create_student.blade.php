<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Create')

@section('content')

@if(session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="mb-0">Create Student</h3>
    <a href="{{ route('admin.studentlist') }}" class="btn btn-danger">Back</a>
  </div>

  <div class="card-body">
    <div class="table-responsive text-nowrap">
      <form action="{{ route('admin.studentstore') }}" method="POST" class="container mt-4">
        @csrf
        <div class="row g-3">
          
          <div class="form-floating form-floating-outline col-md-6">
            <input type="text" class="form-control" id="student_name" name="student_name" placeholder="Enter full name" value="{{ old('student_name') }}">
            <label for="student_name">Full Name<span class="text-danger">*</span></label>
            @error('student_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          
          <div class="form-floating form-floating-outline col-md-6">
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
            <label for="date_of_birth">Date of Birth<span class="text-danger">*</span></label>
            @error('date_of_birth')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

        
          <div class="form-floating form-floating-outline col-md-4">
            <select class="form-select" id="gender" name="gender" value="{{ old('gender') }}">
              <option value="">Select Gender</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
            <label for="gender">Gender<span class="text-danger">*</span></label>
            @error('gender')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         
          <div class="form-floating form-floating-outline col-md-4">
            <input type="email" class="form-control" id="email" name="email" placeholder="student@example.com" value="{{ old('email') }}">
            <label for="email">Email Address<span class="text-danger">*</span></label>
            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter contact number" value="{{ old('phone_number') }}">
            <label for="phone_number">Contact Number<span class="text-danger">*</span></label>
            @error('phone_number')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         
          <div class="form-floating form-floating-outline col-md-6">
            <input type="text" class="form-control" id="parent_name" name="parent_name" placeholder="Enter parent name" value="{{ old('parent_name') }}">
            <label for="parent_name">Parent/Guardian Name<span class="text-danger">*</span></label>
            @error('parent_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         
          <div class="form-floating form-floating-outline col-md-6">
            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address" value="{{ old('address') }}"></textarea>
            <label for="address">Address<span class="text-danger">*</span></label>
            @error('address')<small class="text-danger">{{ $message }}</small>@enderror
          </div>
          <h4>Admission Details</h4>
          
          <div class="form-floating form-floating-outline col-md-4">
            <select name="session_id" class="form-select" id="session_id" value="{{ old('session_id') }}">
              <option value="">Select Session<span class="text-danger">*</span></option>
              @foreach ($sessions as $session)
                <option value="{{ $session->id }}">{{ $session->session_name }}</option>
              @endforeach
            </select>
            <label for="session_id">Session<span class="text-danger">*</span></label>
            @error('session_id')<small class="text-danger">{{ $message }}</small>@enderror
          </div>


          <div class="form-floating form-floating-outline col-md-4">
            <select name="class_id" class="form-select" id="class_id" value="{{ old('class_id') }}">
              <option value="">Select Class</option>
              @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}">{{ $classroom->class }}</option>
              @endforeach
            </select>
            <label for="class_id">Class<span class="text-danger">*</span></label>
            @error('class_id')<small class="text-danger">{{ $message }}</small>@enderror
          </div>
          
          <div class="form-floating form-floating-outline col-md-4">
            <select name="section_id" class="form-select" id="section_id">
                <option value="">Select Section</option>
                
            </select>
            <label for="section_id">Section<span class="text-danger">*</span></label>
            @error('section_id')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          
          <div class="form-floating form-floating-outline col-md-2">
            <input type="number" class="form-control" id="roll_number" name="roll_number" placeholder="Roll No" value="{{ old('roll_number', $admission->roll_number ?? '') }}">
            <label for="roll_number">Roll No<span class="text-danger">*</span></label>
            @error('roll_number')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          
          <div class="form-floating form-floating-outline col-md-2">
            <input type="date" class="form-control" id="admission_date" name="admission_date" value="{{ old('admission_date', $admission->admission_date ?? '') }}">
            <label for="admission_date">Admission Date<span class="text-danger">*</span></label>
            @error('admission_date')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          
          <div class="text-end">
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
<script>
  $(document).ready(function() {
    $('#class_id').on('change', function() {
      var classId = $(this).val();
      $('#section_id').html('<option value="">Loading...</option>');

      if (classId) {
        $.ajax({
            url: "{{ route('admin.student.get-sections') }}",
            type: 'GET',
            dataType: 'json',
            data: { classId: classId },
            success: function(response) {
                if(response.success){
                    console.log(response.sections);
                    // You can loop here to populate dropdown, etc
                    $('#section_id').empty();
                    $('#section_id').append('<option value="">Select Section</option>');
                    $.each(response.sections, function(key, section) {
                        $('#section_id').append('<option value="'+section.section+'">'+section.section+'</option>');
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr);
            }
        });
      } else {
        $('#section_id').html('<option value="">Select Section</option>');
      }
    });
  });
</script>
