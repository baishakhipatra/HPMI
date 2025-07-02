<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<!-- Select2 JS -->
@extends('layouts/contentNavbarLayout')

@section('title', 'Create - Teacher')

@section('content')

<div class="card p-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="mb-0 text-primary">Create Teacher</h3>
    <a href="{{ route('admin.teacher.index') }}" class="btn btn-danger">
      <i class="ri-arrow-left-line"></i> Back
    </a>
  </div>

  <div class="card-body">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.teacher.store') }}" method="POST">
        @csrf

        {{-- Row 1: Teacher ID, Full Name, Hidden User Type --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <input type="text" name="user_id" value="{{ old('user_id', $user_id) }}" class="form-control" readonly>
                    <label>Teacher ID</label>
                    @error('user_id') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating form-floating-outline">
                    <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}">
                    <label>Full Name</label>
                    @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>
            <input type="hidden" name="user_type" value="Teacher">
            <input type="hidden" name="designation_id" value="1">
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
                    <input type="text" name="mobile" class="form-control" placeholder="Mobile" value="{{ old('mobile') }}">
                    <label>Phone</label>
                    @error('mobile') <p class="text-danger small">{{ $message }}</p> @enderror
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

        {{-- Row 3: DOJ, Qualifications, Address --}}
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
                    <input type="text" name="qualifications" class="form-control" placeholder="Qualifications" value="{{ old('qualifications') }}">
                    <label>Qualifications</label>
                    @error('qualifications') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <textarea name="address" class="form-control" placeholder="Address" style="height: 70px;">{{ old('address') }}</textarea>
                    <label>Address</label>
                    @error('address') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <select name="classes_assigned[]" id="classDropdown" class="form-control" multiple>
                        {{-- <option value="">-- Select Class --</option> --}}
                        @foreach($classLists as $class)
                            <option value="{{ $class->id }}">{{ $class->class }}</option>
                        @endforeach
                    </select>
                    <label>Class Assigned</label>
                    @error('classes_assigned') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-floating form-floating-outline">
                    <select name="subjects_taught[]" id="subjectDropdown" class="form-control" multiple>
                        <option value="">-- Select Subject --</option>
                    </select>
                    <label>Subject Taught</label>
                    @error('subjects_taught') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Password Field --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-password-toggle">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline flex-grow-1">
                            <input type="password" id="password" class="form-control" name="password" placeholder="********" />
                            <label for="password">Password</label>
                            @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                        <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line ri-20px"></i></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4 py-2">Create</button>
        </div>
         
    </form>
  </div>

</div>
@endsection

<script>
    $(document).ready(function () {

      $('#classDropdown').on('change', function () {

        var classIds = $(this).val(); // get multiple selected values
        $('#subjectDropdown').html('<option value="">Loading...</option>');
        if (classIds.length > 0) {
          $.ajax({
            url: "{{ route('admin.getSubjectsByClass') }}",
            type: "POST",
            data: {
              _token: '{{ csrf_token() }}',
              'class_ids[]': classIds
            },
            traditional: true, // to send array properly in GET
            success: function (response) {
              if (response.data.length > 0) {
                 $('#subjectDropdown').html('<option value="">-- Select Subject --</option>');
                $.each(response.data, function (key, item) {
                  if (item.subject && item.class_list) {
                    var subjectName = item.subject.sub_name;
                    var className = item.class_list.class;
                    var label = 'Class ' + className + ' - ' + subjectName;

                    $('#subjectDropdown').append('<option value="' + item.id + '">' + label + '</option>');
                  }
                });
              } else {
                $('#subjectDropdown').html('<option value="">No subjects available</option>');
              }
            }

          });
        } else {
          $('#subjectDropdown').html('<option value="">-- Select Subject --</option>');
        }
      });
    });
</script>