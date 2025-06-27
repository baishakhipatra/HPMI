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
    <a href="{{ route('admin.studentlist') }}" class="btn btn-danger">
      <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
  </div>

  <div class="card-body">

      <!-- School Information -->
    <div class="mb-4 border p-3">
      <h5 class="mb-3"><strong>Name of the School:</strong> Holy Palace Multipurpose Institute</h5>
      <p class="mb-1"><strong>Village/Ward:</strong> T114 Teghoria Main Road</p>
      <p class="mb-1"><strong>Circle:</strong> ———</p>
      <p class="mb-1"><strong>District:</strong> Kolkata</p>
      <p class="mb-1"><strong>UDISE+ Code of School:</strong> 19115101102</p>
      <p class="mb-1"><strong>Email of School:</strong> ———</p>
      <p class="mb-1"><strong>School Website:</strong> <a href="https://www.hpmi.in/" target="_blank">https://www.hpmi.in/</a></p>
      <p class="mb-0"><strong>Phone no. of School:</strong> 9433305657, 9330629644</p>
    </div>
    <div class="table-responsive text-nowrap">
      <form action="{{ route('admin.studentstore') }}" method="POST" class="container mt-4" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="mb-3 col-md-12">
                <label for="image" class="form-label">Photograph of Student</label>
                <input type="file" class="form-control  @error('image') is-invalid @enderror" 
                      name="image" id="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
          
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="student_name" name="student_name" placeholder="Enter full name" value="{{ old('student_name') }}">
            <label for="student_name">Name of the Student<span class="text-danger">*</span></label>
            @error('student_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          
          <div class="form-floating form-floating-outline col-md-4">
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
            <label for="date_of_birth">Date of Birth<span class="text-danger">*</span></label>
            @error('date_of_birth')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

        
          <div class="form-floating form-floating-outline col-md-4">
            <select class="form-select" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <label for="gender">Gender<span class="text-danger">*</span></label>
            @error('gender')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         <h4> Personal Details:</h4>
         {{-- Aadhaar No --}}
          <div class="form-floating form-floating-outline col-md-12">
            <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" placeholder="Enter your Aadhaar No." value="{{ old('aadhar_no') }}">
            <label for="aadhar_no">Aadhaar No.<span class="text-danger">*</span></label>
            @error('aadhar_no')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Blood Group --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="blood_group" name="blood_group" placeholder="Enter Blood Group" value="{{ old('blood_group') }}">
            <label for="blood_group">Blood Group<span class="text-danger">*</span></label>
            @error('blood_group')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Height --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="height" name="height" placeholder="Enter Height" value="{{ old('height') }}">
            <label for="height">Height<span class="text-danger">*</span></label>
            @error('height')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- weight --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="weight" class="form-control" id="weight" name="weight" placeholder="" value="{{ old('weight') }}">
            <label for="weight">Weight<span class="text-danger">*</span></label>
            @error('weight')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Father's Name --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father's Name" value="{{ old('father_name') }}">
            <label for="father_name">Father's Name<span class="text-danger">*</span></label>
            @error('father_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Mother's Name --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="mother_name" name="mother_name" placeholder="Mother's Name" value="{{ old('mother_name') }}">
            <label for="mother_name">Mother's Name<span class="text-danger">*</span></label>
            @error('mother_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Guardian Name --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="parent_name" name="parent_name" placeholder="Enter parent name" value="{{ old('parent_name') }}">
            <label for="parent_name">Guardian's Name<span class="text-danger">*</span></label>
            @error('parent_name')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Email --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="email" class="form-control" id="email" name="email" placeholder="student@example.com" value="{{ old('email') }}">
            <label for="email">Email Address<span class="text-danger">*</span></label>
            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
          </div>
      
          {{-- Phone Number --}}
          <div class="form-floating form-floating-outline col-md-4">
            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter contact number" value="{{ old('phone_number') }}">
            <label for="phone_number">Contact Number<span class="text-danger">*</span></label>
            @error('phone_number')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

         
          {{-- Address --}}
          <div class="form-floating form-floating-outline col-md-6">
            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address">{{ old('address') }}</textarea>
            <label for="address">Student's Address<span class="text-danger">*</span></label>
            @error('address')<small class="text-danger">{{ $message }}</small>@enderror
          </div>

          {{-- Divyang --}}
          <div class="col-md-6">
            <label class="form-label d-block">Divyang<span class="text-danger">*</span></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="divyang" id="divyang_yes" value="Yes" {{ old('divyang') == 'Yes' ? 'checked' : '' }}>
                <label class="form-check-label" for="divyang_yes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="divyang" id="divyang_no" value="No" {{ old('divyang', 'No') == 'No' ? 'checked' : '' }}>
                <label class="form-check-label" for="divyang_no">No</label>
            </div>
            @error('divyang')<br><small class="text-danger">{{ $message }}</small>@enderror
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

      function ucwords(str) {
        return str. replace(/\b\w/g, function(char) {
          return char.toUpperCase();
        });
      }

      if (classId) {
        $.ajax({
            url: "{{ route('admin.student.get-sections') }}",
            type: 'GET',
            dataType: 'json',
            data: { classId: classId },
            success: function(response) {
                if(response.success){
                    //console.log(response.sections);
                    // You can loop here to populate dropdown, etc
                    $('#section_id').empty();
                    $('#section_id').append('<option value="">Select Section</option>');
                    $.each(response.sections, function(key, section) {
                        $('#section_id').append('<option value="'+section.section+'">'+ucwords(section.section)+'</option>');
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
