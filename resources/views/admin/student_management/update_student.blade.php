@extends('layouts/contentNavbarLayout')

@section('title', 'Update - student')

@section('content')
@if(session('success'))
  <div class="alert alert-success" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        <div class="card">
          <!-- Card Header -->
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary">Edit Student</h4>
            <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
              <i class="menu-icon tf-icons ri-arrow-left-line"></i> Back
            </a>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <form action="{{ route('admin.studentupdate', $student->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('POST')

              <div class="row g-3">
                <div class="mb-3 col-md-12">
                  <label for="image" class="form-label">Photograph of Student</label>
                  <input type="file" class="form-control form-control-sm @error('image') is-invalid @enderror"
                    name="image" id="image" accept="image/*">
                  @error('image')
                      <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  @if (!empty($student->image))
                      <div class="mt-2">
                          @if (file_exists(public_path($student->image)))
                              <img src="{{ asset($student->image) }}" class="img-thumbnail" alt="Student Image" style="height: 50px;">
                          @else
                              <img src="{{ asset('assets/img/placeholder.jpg') }}" class="img-thumbnail" alt="No Image" style="height: 50px;">
                          @endif
                      </div>
                  @endif
                </div>

                

          
                <h4>Personal Details:</h4>

                {{-- Student Name --}}
                <div class="form-floating form-floating-outline col-md-3">
                  <input type="text" class="form-control" id="student_name" name="student_name" placeholder="Enter full name"
                        value="{{ old('student_name', $student->student_name) }}">
                  <label for="student_name">Name of the Student<span class="text-danger">*</span></label>
                  @error('student_name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

               
                {{-- Date of Birth --}}
                <div class="form-floating form-floating-outline col-md-3">
                  <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                        value="{{ old('date_of_birth', $student->date_of_birth) }}">
                  <label for="date_of_birth">Date of Birth<span class="text-danger">*</span></label>
                  @error('date_of_birth')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

     
                {{-- Gender --}}
              <div class="form-floating form-floating-outline col-md-3">
                <select class="form-select" id="gender" name="gender">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <label for="gender">Gender<span class="text-danger">*</span></label>
                @error('gender')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Aadhaar No --}}
              <div class="form-floating form-floating-outline col-md-3">
                <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" placeholder="Enter your Aadhaar No."
                      value="{{ old('aadhar_no', $student->aadhar_no) }}">
                <label for="aadhar_no">Aadhaar No.<span class="text-danger">*</span></label>
                @error('aadhar_no')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

             
                {{-- Blood Group --}}
              <div class="form-floating form-floating-outline col-md-4">
                <select class="form-select" id="blood_group" name="blood_group">
                    <option value="">Select Blood Group</option>
                    <option value="A+" {{ old('blood_group', $student->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('blood_group', $student->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('blood_group', $student->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('blood_group', $student->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('blood_group', $student->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('blood_group', $student->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('blood_group', $student->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('blood_group', $student->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
                <label for="blood_group">Blood Group</label>
                @error('blood_group')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Height --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="height" name="height" placeholder="Enter Height"
                      value="{{ old('height', $student->height) }}">
                <label for="height">Height</label>
                @error('height')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Weight --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="weight" name="weight" placeholder="Enter Weight (kg)"
                      value="{{ old('weight', $student->weight) }}">
                <label for="weight">Weight</label>
                @error('weight')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Father's Name --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father's Name"
                      value="{{ old('father_name', $student->father_name) }}">
                <label for="father_name">Father's Name<span class="text-danger">*</span></label>
                @error('father_name')<small class="text-danger">{{ $message }}</small>@enderror
              </div>      
              
                {{-- Mother's Name --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="mother_name" name="mother_name" placeholder="Mother's Name"
                      value="{{ old('mother_name', $student->mother_name) }}">
                <label for="mother_name">Mother's Name<span class="text-danger">*</span></label>
                @error('mother_name')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

                {{-- Guardian's Name --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="parent_name" name="parent_name" placeholder="Enter parent name"
                      value="{{ old('parent_name', $student->parent_name) }}">
                <label for="parent_name">Guardian's Name<span class="text-danger">*</span></label>
                @error('parent_name')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

                {{-- Email --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="email" class="form-control" id="email" name="email" placeholder="student@example.com"
                      value="{{ old('email', $student->email) }}">
                <label for="email">Email Address</label>
                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Phone Number --}}
              <div class="form-floating form-floating-outline col-md-4">
                <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter contact number"
                      value="{{ old('phone_number', $student->phone_number) }}">
                <label for="phone_number">Contact Number<span class="text-danger">*</span></label>
                @error('phone_number')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

             
               {{-- Address --}}
              <div class="form-floating form-floating-outline col-md-4">
                <textarea class="form-control" id="address" name="address" rows="3"
                          placeholder="Enter address">{{ old('address', $student->address) }}</textarea>
                <label for="address">Student's Address<span class="text-danger">*</span></label>
                @error('address')<small class="text-danger">{{ $message }}</small>@enderror
              </div>

              {{-- Divyang --}}
              <div class="col-md-6">
                <label class="form-label d-block">Divyang<span class="text-danger">*</span></label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="divyang" id="divyang_yes" value="Yes"
                          {{ old('divyang', $student->divyang) == 'Yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="divyang_yes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="divyang" id="divyang_no" value="No"
                            {{ old('divyang', $student->divyang) == 'No' ? 'checked' : '' }}>
                      <label class="form-check-label" for="divyang_no">No</label>
                  </div>
                  @error('divyang')<br><small class="text-danger">{{ $message }}</small>@enderror
              </div>

                <h4>Admission Details</h4>
                <div class="form-floating form-floating-outline col-md-4">
                  <select name="session_id" class="form-select" id="session_id">
                      <option value="">Select Session</option>
                      @foreach ($sessions as $session)
                          <option value="{{ $session->id }}" 
                              {{ old('session_id', optional($student->admission)->session_id) == $session->id ? 'selected' : '' }}>
                              {{ $session->session_name }}
                          </option>
                      @endforeach
                  </select>
                  <label for="session_id">Session<span class="text-danger">*</span></label>
                  @error('session_id')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-floating form-floating-outline col-md-4">
                    <select name="class_id" class="form-select" id="class_id">
                        <option value="" disabled>Select Class</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" 
                                {{ old('class_id', optional($student->admission)->class_id ?? null) == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->class }}
                            </option>
                        @endforeach
                    </select>
                    <label for="class_id">Class*</label>
                    @error('class')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
       
                <!-- Section -->
                <div class="form-floating form-floating-outline col-md-4">
                    <select name="section_id" class="form-select" id="section_id">
                          <option value="" disabled>Select Section</option>
                          @if(optional($student->admission)->section)
                          <option value="{{optional($student->admission)->section}}" selected>{{(optional($student->admission)->section)}}</option>
                          @endif
                    </select>
                    <label for="section_id">Section<span class="text-danger">*</span></label>
                    @error('section_id')<small class="text-danger">{{ $message }}</small>@enderror
                </div>


                <div class="form-floating form-floating-outline col-md-3">
                    <input type="number" class="form-control" 
                            id="roll_number" name="roll_number" 
                            value="{{ old('roll_number', optional($student->admission)->roll_number) }}">
                    <label for="roll_number">Roll Number<span class="text-danger">*</span></label>
                    @error('roll_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-floating form-floating-outline col-md-3">
                    <input type="date" class="form-control" 
                            id="admission_date" name="admission_date" 
                            value="{{ old('admission_date', optional($student->admission)->admission_date ?? null) }}">
                    <label for="admission_date">Admission Date</label>
                    @error('admission_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


              <input type="hidden" name="id" value="{{ $student->id }}">
              <input type="hidden" name="admission_id" value="{{ optional($student->admission)->id }}">

              
              <div class="text-end">
                <button type="submit" class="btn btn-primary px-4 py-2">
                  <i class="fas fa-user-plus me-2"></i> Update
                </button>
              </div>
            </form>
          </div>

        </div>

      </div>
    </div>
  </div>
</section>

@endsection
@section('scripts')
<script>
  $(document).ready(function() {
      function loadSections(classId, selectedSection = null) {
          $('#section_id').html('<option value="">Loading...</option>');

          if (classId) {
              $.ajax({
                  url: "{{ route('admin.student.get-sections') }}",
                  type: 'GET',
                  dataType: 'json',
                  data: { classId: classId },
                  success: function(response) {
                      $('#section_id').empty().append('<option value="">Select Section</option>');
                      $.each(response.sections, function(key, section) {
                          let selected = (section.section == selectedSection) ? 'selected' : '';
                          $('#section_id').append('<option value="'+section.section+'" '+selected+'>'+section.section+'</option>');
                      });
                  }
              });
          } else {
              $('#section_id').html('<option value="">Select Section</option>');
          }
      }

      $('#class_id').on('change', function() {
          let classId = $(this).val();
          loadSections(classId);
      });

      let existingClassId = "{{ old('class_id', $admission->class_id ?? '') }}";
      let existingSection = "{{ old('section_id', $admission->section ?? '') }}";

      if(existingClassId){
          loadSections(existingClassId, existingSection);
      }
  });

</script>
@endsection