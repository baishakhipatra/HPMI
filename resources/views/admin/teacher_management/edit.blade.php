<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

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
              <i class="menu-icon tf-icons ri-arrow-left-line"></i> Back
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
                        <input type="text" name="user_id" value="{{ old('user_id', $data->user_id) }}" class="form-control" readonly>
                        <label>Teacher ID</label>
                        @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name', $data->name) }}">
                        <label>Full Name</label>
                        @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
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
                  
                    {{-- Pass $classLists and $selectedClassIds, $selectedSubjectIds from controller --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <select name="classes_assigned[]" id="classDropdown" class="form-control" multiple>
                                    @foreach($classLists as $class)
                                        <option value="{{ $class->id }}" 
                                            {{ in_array($class->id, old('classes_assigned', $selectedClassIds)) ? 'selected' : '' }}>
                                            {{ $class->class }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>Class Assigned</label>
                                @error('classes_assigned') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <select name="subjects_taught[]" id="subjectDropdown" class="form-control" multiple>
                                    {{-- Will be populated dynamically --}}
                                </select>
                                <label>Subjects Taught</label>
                                @error('subjects_taught') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value="{{ $data->id }}">

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
        let selectedSubjects = @json($selectedClassWiseSubjectIds);
        // console.log('selectedSubjectIds', selectedSubjects);

        function fetchSubjects(classIds) {
            $('#subjectDropdown').html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ route('admin.getSubjectsByClass') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    'class_ids[]': classIds
                },
                traditional: true,
                success: function (response) {
                    $('#subjectDropdown').html('');
                    if (response.data.length > 0) {
                        console.log('Response data', response.data);

                        $.each(response.data, function (key, item) {
                            if (item.subject && item.class_list) {
                                var label = 'Class ' + item.class_list.class + ' - ' + item.subject.sub_name;
                                var selected = selectedSubjects.includes(item.id) ? 'selected' : '';
                                $('#subjectDropdown').append('<option value="' + item.id + '" ' + selected + '>' + label + '</option>');
                            }
                        });
                    } else {
                        $('#subjectDropdown').html('<option value="">No subjects available</option>');
                    }
                }
            });
        }

        // Initial load for pre-selected classes
        const preSelectedClasses = $('#classDropdown').val();
        if (preSelectedClasses && preSelectedClasses.length > 0) {
            fetchSubjects(preSelectedClasses);
        }

        // On change
        $('#classDropdown').on('change', function () {
            let classIds = $(this).val();
            selectedSubjects = []; // reset selected subjects on change
            if (classIds.length > 0) {
                fetchSubjects(classIds);
            } else {
                $('#subjectDropdown').html('<option value="">-- Select Subject --</option>');
            }
        });
    });
</script>
