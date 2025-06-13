
@extends('layouts/contentNavbarLayout')

@section('title', 'Teacher - Edit')

@section('content')

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        <div class="card">
          <!-- Card Header -->
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Edit Teacher</h5>
            <a href="{{ route('admin.teacher.index') }}" class="btn btn-sm btn-danger">
              <i class="menu-icon tf-icons ri-arrow-left-line"></i></i> Back
            </a>
          </div>

          <!-- Card Body -->
            <div class="card-body">
                <form action="{{ route('admin.teacher.update') }}" method="POST">
                    @csrf
                    @method('POST') {{-- Update to PUT if needed --}}

                    {{-- Row 1: Name, Teacher ID, Type --}}
                    <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name', $data->name) }}">
                        <label>Full Name</label>
                        @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                        <input type="text" name="user_id" value="{{ old('user_id', $data->user_id) }}" class="form-control" readonly>
                        <label>Teacher ID</label>
                        @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                        <input type="text" name="user_type" value="{{ old('user_type', $data->user_type) }}" class="form-control" readonly>
                        <label>Type</label>
                        @error('user_type') <p class="text-danger small">{{ $message }}</p> @enderror
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
                        <input type="text" name="mobile" class="form-control" placeholder="Mobile" value="{{ old('mobile', $data->mobile) }}">
                        <label>Phone</label>
                        @error('mobile') <p class="text-danger small">{{ $message }}</p> @enderror
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

                    {{-- Row 3: DOJ, Qualifications, Address --}}
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
                        <input type="text" name="qualifications" class="form-control" placeholder="Qualifications" value="{{ old('qualifications', $data->qualifications) }}">
                        <label>Qualifications</label>
                        @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                        <textarea name="address" class="form-control" placeholder="Address" style="height: 100px">{{ old('address', $data->address) }}</textarea>
                        <label>Address</label>
                        @error('address') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    </div>

                    {{-- Row 4: Subject Taught, Class Assigned --}}
                  
                    @php
                        $classLists = \App\Models\ClassList::all();
                    @endphp
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <select name="classes_assigned" id="classDropdown" class="form-control">
                                    <option value="">-- Select Class --</option>
                                    @foreach($classLists as $class)
                                      <option value="{{ $class->id}}" {{ old('class_assigned', $data->classes_assigned) == $class->id ? 'selected' : ''}}>
                                        {{$class->class}}
                                      </option>
                                    @endforeach
                                </select>
                                <label>Class Assigned</label>
                                @error('classes_assigned') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                              <select name="subjects_taught" id="subjectDropdown" class="form-control">
                                <option value="">--Select Subject--</option>
                                @if($data->classes_assigned)
                                  @php
                                    $subjects = \App\Models\ClassWiseSubject::with('subject')
                                    ->where('class_id', $data->classes_assigned)
                                    ->get()
                                    ->pluck('subject')
                                    ->filter();
                                  @endphp
                                  @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subjects_taught', $data->subjects_taught) == $subject->id ? 'selected' : ''}}>
                                      {{ $subject->sub_name}}
                                    </option>
                                  @endforeach
                                @endif  
                              </select>    
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$data->id}}">
                    <div class="text-end">
                      <button type="submit" class="btn btn-primary px-4 py-2">Update</button>
                    </div>
                </form>
            </div>

          <!-- End Card Body -->

        </div>

      </div>
    </div>
  </div>
</section>

@endsection
<script>
  $(document).ready(function () {
    $('#classDropdown').on('change', function () {
      var classId = $(this).val();
      $('#subjectDropdown').html('<option value="">Loading...</option>');
      if (classId) {
        $.ajax({
          url: "{{ route('admin.getSubjectsByClass') }}",
          type: "GET",
          data: { class_id: classId },
          success: function (data) {
            $('#subjectDropdown').html('<option value="">-- Select Subject --</option>');
            $.each(data, function (key, subject) {
              $('#subjectDropdown').append('<option value="' + subject.id + '">' + subject.sub_name + '</option>');
            });
          },
        });
      } else {
        $('#subjectDropdown').html('<option value="">-- Select Subject --</option>');
      }
    });
  });
</script>
