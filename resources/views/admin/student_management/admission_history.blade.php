<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - admission')

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
                + New Class Admission
            </a>

            <a href="{{ route('admin.student.readmission.index') }}" class="btn btn-danger btn-sm ms-2">
                Back
            </a>
        </div>
    </div>


  <div class="px-3 py-2">
    {{-- <form action="" method="get">
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
    </form> --}}
  </div>

    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table">
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
                            <td>{{date('d-m-Y',strtotime($history->admission_date))}}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button >
                                    <div class="dropdown-menu">
                                        <button 
                                                type="button" 
                                                class="btn editBtn" 
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
                                    </div>
                                </div>
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
                                        <option value="">Select Session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('session_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                
                                <div class="form-group mb-2">
                                    <label>Class</label>
                                    <select class="form-select" name="class_id" id="class_id_modal" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->class }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>


                                <div class="form-group mb-2">
                                    <label>Section</label>
                                    <select class="form-select" name="section_id" id="section_id_modal" required>
                                        <option value="">Select Section</option>
                                    </select>
                                    @error('section_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                        
                                <div class="form-group mb-2">
                                    <label>Roll Number</label>
                                    <input type="number" class="form-control" name="roll_number" id="roll_number_modal" required>
                                    @error('roll_number') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                 
                                <div class="form-group mb-2">
                                    <label>Admission Date</label>
                                    <input type="date" class="form-control" name="admission_date" id="admission_date_modal" required>
                                    @error('admission_date') <small class="text-danger">{{ $message }}</small> @enderror
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


    $(document).ready(function () {

        $(document).on('click', '.editBtn', function () {
       
            $('#admission_id').val($(this).data('id'));
            $('#session_id_modal').val($(this).data('session_id'));
            $('#class_id_modal').val($(this).data('class_id'));
            $('#roll_number_modal').val($(this).data('roll_number'));
            $('#admission_date_modal').val($(this).data('admission_date'));

            let classId = $(this).data('class_id');
            let sectionValue = $(this).data('section'); 

            loadSections(classId, sectionValue);
        });

        $('#class_id_modal').on('change', function () {
            let classId = $(this).val();
            loadSections(classId, null);
        });

    
        function loadSections(classId, selectedSection = null) {
            $('#section_id_modal').html('<option value="">Loading...</option>');

            if (classId) {
                $.ajax({
                    url: "{{ route('admin.student.get-sections') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { classId: classId },
                    success: function (response) {
                        if (response.success) {
                            $('#section_id_modal').empty().append('<option value="">Select Section</option>');
                            $.each(response.sections, function (key, section) {
                                let isSelected = (section.section == selectedSection) ? 'selected' : '';
                                $('#section_id_modal').append('<option value="' + section.section + '" ' + isSelected + '>' + section.section + '</option>');
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                    }
                });
            } else {
                $('#section_id_modal').html('<option value="">Select Section</option>');
            }
        }

    });


    $(document).ready(function() {
        $('#class_id_modal').on('change', function() {
            let classId = $(this).val();
            loadSections(classId);
        });
    });

</script>