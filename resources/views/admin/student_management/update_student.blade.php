<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            <h4 class="mb-0">Edit Student</h4>
            <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
              <i class="menu-icon tf-icons ri-arrow-left-line"></i></i> Back
            </a>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <form action="{{ route('admin.studentupdate', $student->id) }}" method="POST">
              @csrf
              @method('POST')

              <div class="row g-3">
               
                <div class="form-floating form-floating-outline col-md-6">
                    <input type="text" class="form-control" 
                            id="student_name" name="student_name" 
                            value="{{ old('student_name', $student->student_name) }}">
                    <label for="student_name">Student Name</label>
                    @error('student_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

               
                <div class="form-floating form-floating-outline col-md-6">
                   
                    <input type="date" class="form-control" 
                            id="date_of_birth" name="date_of_birth" 
                            value="{{ old('date_of_birth', $student->date_of_birth) }}">
                    <label for="date_of_birth">Date of Birth</label>
                    @error('date_of_birth')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

     
                <div class="form-floating form-floating-outline col-md-4">
                    <select class="form-select" name="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <label for="gender">Gender*</label>
                    @error('gender')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

          
                <div class="form-floating form-floating-outline col-md-8">
                  <input type="text" class="form-control" 
                          id="parent_name" name="parent_name" 
                          value="{{ old('parent_name', $student->parent_name) }}">
                  <label for="parent_name">Parent/Guardian Name</label>
                  @error('parent_name')
                      <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

             
                <div class="form-floating form-floating-outline col-md-6">
                    
                    <input type="email" class="form-control" 
                            id="email" name="email" 
                            value="{{ old('email', $student->email) }}">
                    <label for="email">Email</label>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

           
                <div class="form-floating form-floating-outline col-md-6">
                    <input type="text" class="form-control" 
                            id="phone_number" name="phone_number" 
                            value="{{ old('phone_number', $student->phone_number) }}">
                    <label for="phone_number">Phone Number</label>
                    @error('phone_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

             
                <div class="form-floating form-floating-outline col-12">
                    <textarea class="form-control" 
                                id="address" name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                    <label for="address">Address</label>
                    @error('address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <h4>Admission Details</h4>
                <div class="form-floating form-floating-outline col-md-4">
                  <select name="session_id" class="form-select" id="session_id">
                    <option value="" disabled>Select Session</option>
                    @foreach ($sessions as $session)
                      <option value="{{ $session->id }}" {{ old('session_id', optional($student->admission)->session_id ?? null) == $session->id ? 'selected' : '' }}>{{ $session->session_name }}</option>
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
                          <option value="{{optional($student->admission)->section}}" selected>{{optional($student->admission)->section}}</option>
                          @endif
                    </select>
                    <label for="section_id">Section<span class="text-danger">*</span></label>
                    @error('section_id')<small class="text-danger">{{ $message }}</small>@enderror
                </div>


                <div class="form-floating form-floating-outline col-md-2">
                    <input type="number" class="form-control" 
                            id="roll_number" name="roll_number" 
                            value="{{ old('roll_number', optional($student->admission)->roll_number) }}">
                    <label for="roll_number">Roll Number</label>
                    @error('roll_number')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-floating form-floating-outline col-md-2">
                    <input type="date" class="form-control" 
                            id="admission_date" name="admission_date" 
                            value="{{ old('admission_date', optional($student->admission)->admission_date ?? null) }}">
                    <label for="admission_date">Admission Date</label>
                    @error('admission_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


              <input type="hidden" name="id" value="{{ $student->id }}">
              
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