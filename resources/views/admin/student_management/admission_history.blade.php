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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Student Name: {{ucwords($student->student_name)}} ({{$student->student_id}})</h4>
        
        <div class="d-flex">
            <a href="{{route('admin.student.readmission', $student->id)}}" class="btn btn-primary btn-sm">
                + Re-Admission
            </a>

            <a href="{{ route('admin.studentlist') }}" class="btn btn-danger btn-sm ms-2">
                Back
            </a>
        </div>
    </div>


  <div class="px-3 py-2">
    <form action="" method="get">
      <div class="row">
        <div class="col-md-6"></div>
          <div class="col-md-6">  
            <div class="d-flex justify-content-end">
              <div class="form-group me-2 mb-0">
                <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" value="{{ request()->input('keyword') }}" placeholder="Search something...">
              </div>
              <div class="form-group mb-0">
                <div class="btn-group">
                  <button type="submit" class="btn btn-sm btn-primary">
                    <i class="menu-icon tf-icons ri-filter-3-line"></i>
                  </button>
                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-toggle="tooltip" title="Clear filter">
                    <i class="menu-icon tf-icons ri-close-line"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
      </div>
    </form>
  </div>

    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered">
                <thead>
                   <tr>
                        <th>Session</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Roll Number</th>
                        <th>Admission Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admissionHistories as $history)
                        <tr>
                            <td>{{ $history->session->session_name ?? '-' }}</td> 
                            <td>{{ $history->class->class ?? '-' }}</td> 
                            <td>{{ $history->section }}</td>
                            <td>{{ $history->roll_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($history->admission_date)->format('d-m-Y') }}</td>
                            <td>
                               <button 
                                    type="button" 
                                    class="btn btn-sm btn-info editBtn" 
                                    data-id="{{ $history->id }}"
                                    data-session_id="{{ $history->session_id }}"
                                    data-class_id="{{ $history->class_id }}"
                                    data-section="{{ $history->section }}"
                                    data-roll_number="{{ $history->roll_number }}"
                                    data-admission_date="{{ $history->admission_date }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal">
                                    <i class="ri-pencil-line me-1"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="editForm" method="POST" action="{{ route('admin.student.admissionhistoryUpdate') }}">
                        @csrf
                        <input type="hidden" name="id" id="admission_id">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Admission Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                           
                            <div class="form-group mb-2">
                                <label>Session</label>
                                <select class="form-select" name="session_id" id="session_id_modal" required>
                                @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                                @endforeach
                                </select>
                            </div>

                            
                            <div class="form-group mb-2">
                                <label>Class</label>
                                <select class="form-select" name="class_id" id="class_id_modal" required>
                                @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class }}</option>
                                @endforeach
                                </select>
                            </div>

                            
                            <div class="form-floating col-md-4">
                                <select name="section_id" class="form-select" id="section_id" required>
                                <option value="">Select Section</option>
                                </select>
                                <label for="section_id">Section <span class="text-danger">*</span></label>
                                @error('section_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>

                           
                            <div class="form-group mb-2">
                                <label>Roll Number</label>
                                <input type="number" class="form-control" name="roll_number" id="roll_number_modal" required>
                            </div>

                           
                            <div class="form-group mb-2">
                                <label>Admission Date</label>
                                <input type="date" class="form-control" name="admission_date" id="admission_date_modal" required>
                            </div>
                            </div>

                            <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>  
    </div>
  
</div>
@endsection

<script>
 document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('admission_id').value = this.dataset.id;

            // dropdown binding properly:
            let sessionSelect = document.getElementById('session_id_modal');
            let classSelect = document.getElementById('class_id_modal');
            let sectionInput = document.getElementById('section_modal');
            let rollInput = document.getElementById('roll_number_modal');
            let admissionDateInput = document.getElementById('admission_date_modal');

            sessionSelect.value = this.dataset.session_id;
            classSelect.value = this.dataset.class_id;
            sectionInput.value = this.dataset.section;
            rollInput.value = this.dataset.roll_number;
            admissionDateInput.value = this.dataset.admission_date;
        });
    });

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