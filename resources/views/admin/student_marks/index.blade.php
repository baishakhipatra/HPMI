<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Marks - List')

@section('content')
    <style>
        .session-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .session-header {
            font-size: 18px;
            font-weight: bold;
            color: #444;
            margin-bottom: 15px;
            border-bottom: 2px solid #a312cf;
            padding-bottom: 5px;
        }

        table.marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .marks-table th,
        .marks-table td {
            border: 1px solid #ccc;
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        .marks-table th {
            background-color: #d3c4f3;
            font-weight: 600;
        }

        .grade {
            font-weight: bold;
            color: #193687;
        }

        .actions-cell {
            white-space: nowrap;
        }
    </style>
    @if($errors->has('term_required'))
        <div class="alert alert-danger">
            {{ $errors->first('term_required') }}
        </div>
    @endif

    @php
        $errors = session('errors');
    @endphp
    @if ($errors && $errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Marks Management</h3>
                <small class="text-muted">Manage student marks</small>
            </div>
            <div>
                <a href="{{ route('admin.student-marks.export', [
                            'student_name' => request('student_name'),
                            'class_filter' => request('class_filter'),
                            'subject_filter' => request('subject_filter'),
                            'session_filter'  => request('session_filter')
                        ]) }}" 
                    class="btn buttons-collection btn-outline-secondary dropdown-toggle waves-effect" 
                    data-toggle="tooltip" title="Export Data">
                        Export
                </a>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMarksModal">
                + Add Marks
                </button>
            </div>
        </div>
        <form action="{{ route('admin.studentmarklist')}}" method="GET">
            <div class="row mb-4">
                <div class="col-md-4 mb-2">
                    <input type="text" id="student_name" name="student_name" class="form-control" placeholder="Search by student name..." value="{{ request('student_name') }}">
                </div>
                <div class="form-floating form-floating-outline col-md-2">
                    <select id="session_filter" name="session_filter" class="form-select">
                        <option value="">All sessions</option>
                        @foreach($academicSessions as $academic)
                            <option value="{{ $academic->id }}" {{ request('session_filter') == $academic->id ? 'selected' : '' }}>{{ $academic->session_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-floating form-floating-outline col-md-2">
                    <select id="class_filter" name="class_filter" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classOptions as $class)
                            <option value="{{ $class['id'] }}" {{ request('class_id') == $class['id'] ? 'selected' : '' }}>{{ $class['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-floating form-floating-outline col-md-2 mb-2">
                    <select id="subject_filter" name="subject_filter" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ ucwords($subject->sub_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end"> 
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.studentmarklist') }}" class="btn btn-secondary ms-2">Reset</a>
                </div>
            </div>
        </form>

        <div class="card shadow-sm">
          <div class="card-body">
            <div class="session-card mb-4">
              @forelse($groupedMarks as $groupKey => $studentMarks)
              @php
              $first = $studentMarks->first();
              $student = $first->studentAdmission->student->student_name ?? '-';
              $session = $first->studentAdmission->session->session_name ?? '-';
              $class = $first->studentAdmission->class->class ?? '-';
              @endphp

              <div class="student-section mb-3">
                <div class="session-header fw-bold">
                  Session: {{ $session }} | Class: {{ strtoupper($class) }} | Student: {{ ucwords($student) }}
                </div>

                <div class="table-responsive">
                  <table class="table table-bordered marks-table mt-2">
                    <thead class="table-light">
                      <tr>
                        <th>Subject</th>
                        <th>Midterm</th>
                        <th>Final</th>
                        <th>Total</th>
                        <th>Average</th>
                        <th>Grade</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($studentMarks as $mark)
                      @php
                          $mid = $mark->mid_term_stu_marks ?? 0;
                          $final = $mark->final_exam_stu_marks ?? 0;

                          // Count how many terms have marks (to avoid dividing by 2 if one is null)
                          $count = 0;
                          if (!is_null($mark->mid_term_stu_marks)) $count++;
                          if (!is_null($mark->final_exam_stu_marks)) $count++;

                          $average = $count > 0 ? ($mid + $final) / $count : 0;

                          $total = $mid + $final; // Just for display if needed
                          $grade = calculateGrade($average);
                      @endphp
                      <tr>
                        <td>{{ ucwords($mark->subjectlist->sub_name ?? '-') }}</td>
                        <td>{{ $mark->mid_term_stu_marks .'/'.$mark->mid_term_out_off ?? '-' }}</td>
                        <td>{{ $mark->final_exam_stu_marks.'/'.$mark->final_exam_out_off ?? '-' }}</td>
                        <td><strong>{{ $total }}</strong></td>
                        <td>{{ number_format($average, 2) }}</td>
                        <td><span class="grade">{{ $grade }}</span></td>
                        <td class="actions-cell">
                          <div class="btn-group" role="group" aria-label="Mark Actions">
                            {{-- Edit Button --}}
                            <button type="button"
                              class="btn btn-sm btn-icon btn-outline-dark editMarksBtn"
                              data-url="{{ route('admin.student-marks.getData', $mark->id) }}"
                              data-bs-toggle="tooltip"  title="Edit">                                 
                              <i class="ri-pencil-line"></i>
                            </button>

                            {{-- Delete Button --}}
                            <button type="button"
                              class="btn btn-sm btn-icon btn-outline-danger"
                              onclick="deleteMark({{ $mark->id }})" data-bs-toggle="tooltip" title="Delete">
                              <i class="ri-delete-bin-6-line"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              @empty
              <p class="text-muted">No records found.</p>
              @endforelse
              <div class="d-flex justify-content-end">
                {{ $marks->links() }}
              </div>
            </div>
          </div>
        </div>

        {{-- for add marks --}}
          <div class="modal fade" id="addMarksModal" tabindex="-1" aria-labelledby="addMarksModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">

              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title" id="addMarksModalLabel">Add New Marks</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="StudentMarksStore" enctype="multipart/form-data">
                  @csrf
                
                  <div class="modal-body">
                      <div class="row g-3">
                          {{-- First row: Session, Student, Class --}}
                          <div class="form-floating form-floating-outline col-md-4">
                              <select name="session_id" id="session_id" class="form-select">
                                  <option value="">Select Session</option>
                                  @foreach($sessions as $item)
                                      <option value="{{ $item->session_id }}">{{ $item->session->session_name }}</option>
                                  @endforeach
                              </select>
                              <label for="session_id" class="form-label">Session</label>
                              <div class="text-danger" id="error_session_id"></div>
                          </div>

                          <div class="form-floating form-floating-outline col-md-4">
                              <select name="student_id" id="student_id" class="form-select">
                                  <option value="">Select Student</option>
                              </select>
                              <label for="student_id" class="form-label">Student</label>
                              <div class="text-danger" id="error_student_id"></div>
                          </div>

                          <div class="form-floating form-floating-outline col-md-4">
                              <select name="class_id" id="class_id" class="form-select">
                                  <option value="">Select Class</option>
                                  {{-- Class will be populated by AJAX based on student selection --}}
                              </select>
                              <label for="class_id" class="form-label">Class</label>
                              <div class="text-danger" id="error_class_id"></div>
                          </div>
                      </div>
                      <hr>

                      {{-- Subject & Marks Fields --}}
                      <div id="subject-marks-wrapper">
                          <div class="row g-3 subject-marks-group">
                              <div class="form-floating form-floating-outline col-md-3">
                                  <select name="subject_id[]" class="form-select subject-dropdown">
                                      <option value="">Select Subject</option>
                                      {{-- Initial subjects loaded from controller. More will be added via AJAX and Add More. --}}
                                      @foreach($subjects as $subject)
                                          <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                                      @endforeach
                                  </select>
                                  <label class="form-label">Subject</label>
                              </div>

                              <div class="form-floating form-floating-outline col-md-2">
                                  <select name="mid_term_out_off[]" class="form-select">
                                      <option value="100">100</option>
                                  </select>
                                  <label class="form-label">Mid Term Out Of</label>
                              </div>

                              <div class="form-floating form-floating-outline col-md-2">
                                  <input type="number" name="mid_term_stu_marks[]" class="form-control" placeholder="Mid Term Marks">
                                  <label>Mid Term Student Marks</label>
                              </div>

                              <div class="form-floating form-floating-outline col-md-2">
                                  <select name="final_exam_out_off[]" class="form-select">
                                      <option value="100">100</option>
                                  </select>
                                  <label class="form-label">Final Exam Out Of</label>
                              </div>

                              <div class="form-floating form-floating-outline col-md-2">
                                  <input type="number" name="final_exam_stu_marks[]" class="form-control" placeholder="Final Exam Marks">
                                  <label>Final Exam Student Marks</label>
                              </div>

                              <div class="col-md-1 d-flex align-items-center">
                                  <button type="button" class="btn btn-danger remove-subject-group d-none">×</button>
                              </div>
                          </div>
                      </div>
                      

                      <div class="mt-3">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="addMoreSubject">+ Add More</button>
                      </div>
                  </div>

                  <div class="modal-footer">
                    <div id="formAlert" class="alert d-none" role="alert"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="StudentMarksStoreButton">Save Marks</button>

                  </div>
                </form>
              </div>
            </div>
          </div>

          {{-- for update marks --}}
          <div class="modal fade" id="editMarksModal" tabindex="-1" aria-labelledby="editMarksModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editMarksModalLabel">Edit Marks</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="editMarksForm" method="POST">
                  @csrf
                  {{-- @method('PUT') This is crucial for Laravel's update method --}}

                  <input type="hidden" id="edit_mark_id" name="id"> {{-- This input will hold the mark ID --}}

                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="form-floating form-floating-outline col-md-2">
                        <select name="session_id" id="edit_session_id" class="form-select">
                          <option value="">Select Session</option>
                          @foreach($sessions as $item)
                          <option value="{{ $item->session_id }}">{{ $item->session->session_name }}</option>
                          @endforeach
                        </select>
                        <label for="edit_session_id" class="form-label">Session</label>
                        <div class="text-danger" id="error_session_id"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-6">
                        <select name="student_id" id="edit_student_id" class="form-select">
                          <option value="">Select Student</option>
                          {{-- Students will be loaded here via AJAX --}}
                        </select>
                        <label for="edit_student_id" class="form-label">Student</label>
                        <div class="text-danger" id="error_student_id"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-2">
                        <select name="class_id" id="edit_class_id" class="form-select">
                          <option value="">Select Class</option>
                          {{-- Classes will be loaded here via AJAX --}}
                        </select>
                        <label for="edit_class_id" class="form-label">Class</label>
                        <div class="text-danger" id="error_class_id"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-2">
                        <select name="subject_id" id="edit_subject_id" class="form-select">
                          <option value="">Select Subject</option>
                          {{-- Subjects will be loaded here via AJAX --}}
                        </select>
                        <label for="edit_subject_id" class="form-label">Subject</label>
                        <div class="text-danger" id="error_subject_id"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-3">
                        <select name="mid_term_out_off" id="edit_mid_term_out_off" class="form-select">
                          <option value="">select value</option>
                          <option value="100">100</option>
                          {{-- <option value="">Select</option>
                          @foreach (range(50, 100, 5) as $value)
                          <option value="{{ $value }}">{{ $value }}</option>
                          @endforeach --}}
                        </select>
                        <label for="edit_mid_term_out_off" class="form-label">Mid Term Out Of</label>
                        <div class="text-danger" id="error_mid_term_out_off"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-3">
                        <input type="number" name="mid_term_stu_marks" id="edit_mid_term_stu_marks"
                          class="form-control" placeholder="Enter marks">
                        <label for="edit_mid_term_stu_marks">Mid Term Student Marks</label>
                        <div class="text-danger" id="error_mid_term_stu_marks"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-3">
                        <select name="final_exam_out_off" id="edit_final_exam_out_off" class="form-select">
                          <option value="">select value</option>
                          <option value="100">100</option>
                          {{-- <option value="">Select</option>
                          @foreach (range(50, 100, 5) as $value)
                          <option value="{{ $value }}">{{ $value }}</option>
                          @endforeach --}}
                        </select> 
                        <label for="edit_final_exam_out_off" class="form-label">Final Exam Out Of</label>
                        <div class="text-danger" id="error_final_exam_out_off"></div>
                      </div>

                      <div class="form-floating form-floating-outline col-md-3">
                        <input type="number" name="final_exam_stu_marks" id="edit_final_exam_stu_marks"
                          class="form-control" placeholder="Enter marks">
                        <label for="edit_final_exam_stu_marks">Final Exam Student Marks</label>
                        <div class="text-danger" id="error_final_exam_stu_marks"></div>
                      </div>

                    </div>
                  </div>

                  <div class="modal-footer">
                    <div id="formAlert1" class="alert d-none" role="alert"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editMarksButton">Update Marks</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
    </div>
    
@endsection
{{-- @section('scripts') --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <script>
        
      $(document).ready(function () {
        // Global variable to store subjects fetched by AJAX for 'Add More' functionality
        let fetchedSubjects = [];

        // Handler for storing student marks
        $('#StudentMarksStoreButton').on('click', function (e) {
            e.preventDefault();

            var form = $('#StudentMarksStore')[0];
            var formData = new FormData(form);

            // Reset previous validation messages and styles
            $('#formAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');
            $('.text-danger').text('');
            $('input, select').removeClass('is-invalid');

            $.ajax({
                url: "{{ route('admin.student-marks.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success === true) {
                        $('#formAlert')
                            .removeClass('d-none alert-danger')
                            .addClass('alert alert-success')
                            .text(response.message);

                        $('#StudentMarksStore')[0].reset();
                        // Reset subject-marks wrapper to initial state
                        $('#subject-marks-wrapper').html(`
                            <div class="row g-3 subject-marks-group">
                                <div class="form-floating form-floating-outline col-md-3">
                                    <select name="subject_id[]" class="form-select subject-dropdown">
                                        <option value="">Select Subject</option>
                                        {{-- Re-populate with initial subjects from controller or leave empty --}}
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                                        @endforeach
                                    </select>
                                    <label class="form-label">Subject</label>
                                </div>
                                <div class="form-floating form-floating-outline col-md-2">
                                    <select name="mid_term_out_off[]" class="form-select">
                                        <option value="100">100</option>
                                    </select>
                                    <label class="form-label">Mid Term Out Of</label>
                                </div>
                                <div class="form-floating form-floating-outline col-md-2">
                                    <input type="number" name="mid_term_stu_marks[]" class="form-control" placeholder="Mid Term Marks">
                                    <label>Mid Term Student Marks</label>
                                </div>
                                <div class="form-floating form-floating-outline col-md-2">
                                    <select name="final_exam_out_off[]" class="form-select">
                                        <option value="100">100</option>
                                    </select>
                                    <label class="form-label">Final Exam Out Of</label>
                                </div>
                                <div class="form-floating form-floating-outline col-md-2">
                                    <input type="number" name="final_exam_stu_marks[]" class="form-control" placeholder="Final Exam Marks">
                                    <label>Final Exam Student Marks</label>
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-danger remove-subject-group d-none">×</button>
                                </div>
                            </div>
                        `);
                        // Re-initialize select2 if used on subject dropdown after reset
                        // $('.subject-dropdown').select2({ dropdownParent: $('#addMarksModal') });

                        setTimeout(function () {
                            window.location.reload(); // Reload the page after successful submission
                        }, 2000);
                    }
                },
                error: function (xhr) {
                    $('#formAlert').removeClass('d-none alert-success').addClass('alert alert-danger');
                    if (xhr.status === 422) {
                        let response = xhr.responseJSON;
                        if (response.message) {
                            $('#formAlert').html(response.message);
                        }
                        if (response.errors) {
                            $('.text-danger').text('');
                            $.each(response.errors, function (key, value) {
                                // Handle array errors for subject_id, mid_term_stu_marks etc.
                                if (key.includes('.')) {
                                    let parts = key.split('.');
                                    let fieldName = parts[0]; // e.g., 'subject_id'
                                    let index = parts[1];      // e.g., '0'

                                    // Find the specific input field in the correct group
                                    $(`[name="${fieldName}[]"]`).eq(index).addClass('is-invalid');
                                    // You might need a more specific error message container for indexed fields
                                    // For simplicity, let's just add the class for now.
                                } else {
                                    $('#error_' + key).text(value[0]);
                                    $('#' + key).addClass('is-invalid');
                                }
                            });
                        }
                    } else {
                        $('#formAlert').html('An unexpected error occurred. Please try again.');
                    }
                }
            });
        });

        // Clear validation errors on input/select change
        $('input, select').on('input change', function () {
            var field = $(this).attr('id');
            if (field) { // Check if ID exists (for single fields)
                $('#error_' + field).text('');
                $(this).removeClass('is-invalid');
            } else { // For array fields like subject_id[]
                $(this).removeClass('is-invalid');
            }
        });

        // AJAX for fetching students by session
        $('#session_id').on('change', function () {
            var sessionId = $(this).val();
            $('#student_id').html('<option value="">Loading...</option>');
            $('#class_id').html('<option value="">Select Class</option>'); // Clear class dropdown
            updateInitialSubjectDropdown([]); // Clear subjects when session changes

            if (sessionId) {
                $.ajax({
                    url: "{{ route('admin.get-students-by-session') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { sessionId: sessionId },
                    success: function (response) {
                        if (response.success) {
                            $('#student_id').empty();
                            $('#student_id').append('<option value="">Select Student</option>');
                            $.each(response.students, function (key, student) {
                                $('#student_id').append(new Option(student.name, student.id, false, false));
                            });
                            $('#student_id').val(null).trigger('change'); // clear selection and re-trigger select2 update
                            // Re-initialize select2 for student_id (important if it's within a modal)
                            // $('#student_id').select2({
                            //   placeholder: 'Select Student',
                            //   allowClear: true,
                            //   dropdownParent: $('#addMarksModal'),
                            //   minimumResultsForSearch: 0 // Keep this here too for re-initialization
                            // });
                        }
                    },
                    error: function (xhr) {
                        console.error("Error fetching students:", xhr);
                        $('#student_id').html('<option value="">Error loading students</option>');
                    }
                });
            } else {
                $('#student_id').html('<option value="">Select Student</option>');
                // Clear select2 for student_id if session is cleared
                $('#student_id').select2({
                    placeholder: 'Select Student',
                    allowClear: true,
                    dropdownParent: $('#addMarksModal')
                });
            }
        });

        // AJAX for fetching class and subjects by student and session
        $('#student_id').on('change', function () {
            var student_id = $(this).val();
            var session_id = $('#session_id').val();
            $('#class_id').html('<option value="">Loading...</option>');
            updateInitialSubjectDropdown([]); // Clear subjects when student changes

            if (session_id && student_id) {
                $.ajax({
                    url: "{{ route('admin.get-class-by-session-and-student') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { student_id: student_id, session_id: session_id },
                    success: function (response) {
                        if (response.success) {
                            $('#class_id').empty();
                            $.each(response.classes, function (key, classData) {
                                $('#class_id').append('<option value="' + classData.id + '" selected>' + classData.name + '</option>');
                            });

                            // Update the global fetchedSubjects variable with the new subjects
                            fetchedSubjects = response.subjects;
                            updateInitialSubjectDropdown(fetchedSubjects);

                        } else {
                            $('#class_id').html('<option value="">' + response.message + '</option>');
                            // Also clear subjects if no class found
                            updateInitialSubjectDropdown([]);
                        }
                    },
                    error: function (xhr) {
                        console.error("Error fetching class/subjects:", xhr);
                        $('#class_id').html('<option value="">Error loading class</option>');
                        updateInitialSubjectDropdown([]);
                    }
                });
            } else {
                $('#class_id').html('<option value="">Select class</option>');
                updateInitialSubjectDropdown([]);
            }
        });

        // Function to update the initial subject dropdown in the first row of subject-marks-group
        function updateInitialSubjectDropdown(subjects) {
            let initialSubjectDropdown = $('.subject-marks-group').first().find('.subject-dropdown');
            initialSubjectDropdown.empty().append('<option value="">Select Subject</option>');
            $.each(subjects, function (key, subjectData) {
                initialSubjectDropdown.append('<option value="' + subjectData.id + '">' + subjectData.name + '</option>');
            });
        }


        // Add More Subject-Marks Fields
        $('#addMoreSubject').on('click', function () {
            let wrapper = $('#subject-marks-wrapper');
            let subjectsUsed = [];

            // Collect subjects already selected in existing dropdowns
            wrapper.find('.subject-dropdown').each(function () {
                let val = $(this).val();
                if (val) subjectsUsed.push(val);
            });

            // Filter available subjects based on what's already selected
            let availableSubjects = fetchedSubjects.filter(s => !subjectsUsed.includes(s.id.toString())); // Convert s.id to string for strict comparison

            if (availableSubjects.length === 0) {
                
                toastFire('error', "All available subjects for this class are already selected or no subjects are assigned.");
                return;
            }

           
            let optionsHtml = '<option value="">Select Subject</option>';
            optionsHtml += availableSubjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');

            let group = `
                <div class="row g-3 subject-marks-group mt-3"> <div class="form-floating form-floating-outline col-md-3">
                        <select name="subject_id[]" class="form-select subject-dropdown">
                            ${optionsHtml}
                        </select>
                        <label class="form-label">Subject</label>
                    </div>
                    <div class="form-floating form-floating-outline col-md-2">
                        <select name="mid_term_out_off[]" class="form-select">
                            <option value="100">100</option>
                        </select>
                        <label class="form-label">Mid Term Out Of</label>
                    </div>
                    <div class="form-floating form-floating-outline col-md-2">
                        <input type="number" name="mid_term_stu_marks[]" class="form-control" placeholder="Mid Term Marks">
                        <label>Mid Term Student Marks</label>
                    </div>
                    <div class="form-floating form-floating-outline col-md-2">
                        <select name="final_exam_out_off[]" class="form-select">
                            <option value="100">100</option>
                        </select>
                        <label class="form-label">Final Exam Out Of</label>
                    </div>
                    <div class="form-floating form-floating-outline col-md-2">
                        <input type="number" name="final_exam_stu_marks[]" class="form-control" placeholder="Final Exam Marks">
                        <label>Final Exam Student Marks</label>
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-subject-group">×</button>
                    </div>
                </div>
            `;
            wrapper.append(group);
            // Show the remove button for new groups
            $('.subject-marks-group:not(:first-child) .remove-subject-group').removeClass('d-none');
        });

        // Initialize select2 for student_id on document ready (for initial load)
        // $('#student_id').select2({
        //     placeholder: 'Select Student',
        //     allowClear: true,
        //     dropdownParent: $('#addMarksModal'), // Make sure this matches your modal's ID
        // });

        // Remove Subject-Marks Group
        $(document).on('click', '.remove-subject-group', function () {
            $(this).closest('.subject-marks-group').remove();
            // Re-hide remove button if only one group remains
            if ($('.subject-marks-group').length === 1) {
                $('.subject-marks-group .remove-subject-group').addClass('d-none');
            }
        });

        // Initially hide remove button if only one group is present
        if ($('.subject-marks-group').length === 1) {
            $('.subject-marks-group .remove-subject-group').addClass('d-none');
        }
      });


        // For edit marks modal
        $(document).ready(function() {
            // Function to load students for the edit modal
            function loadEditStudents(sessionId, selectedStudentId = null, selectedClassId = null, selectedSubjectId = null) {
                if (!sessionId) {
                    $('#edit_student_id').empty().append('<option value="">Select Student</option>');
                    // Also clear classes and subjects if session is cleared
                    $('#edit_class_id').empty().append('<option value="">Select Class</option>');
                    $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.get-students-by-session') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: { sessionId: sessionId },
                    success: function (response) {
                        if (response.success) {
                            $('#edit_student_id').empty().append('<option value="">Select Student</option>'); // Always add a default option
                            $.each(response.students, function (i, student) {
                                $('#edit_student_id').append('<option value="' + student.id + '">' + student.name + '</option>');
                            });
                            // Set the selected student after all options are loaded
                            if (selectedStudentId) {
                                // $('#edit_student_id').val(selectedStudentId).trigger('change');
                                $('#edit_student_id').val(selectedStudentId);
                                // Now load classes and subjects based on selected student
                                loadEditClassesAndSubjects(sessionId, selectedStudentId, selectedClassId, selectedSubjectId);
                            }
                            // No need to trigger change here, the loadEditClassesAndSubjects will be called directly in editMarksBtn handler
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching students for edit modal:", xhr);
                    }
                });
            }

            // Function to load classes and subjects for the edit modal
            function loadEditClassesAndSubjects(sessionId, studentId, selectedClassId = null, selectedSubjectId = null) {
                if (!sessionId || !studentId) {
                    $('#edit_class_id').empty().append('<option value="">Select Class</option>');
                    $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.get-class-by-session-and-student') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        session_id: sessionId,
                        student_id: studentId
                    },
                    success: function (response) {
                        if (response.success) {
                            // Populate classes
                            $('#edit_class_id').empty().append('<option value="">Select Class</option>');
                            $.each(response.classes, function (key, classData) {
                                $('#edit_class_id').append('<option value="' + classData.id + '">' + classData.name + '</option>');
                            });
                            // Set the selected class after all options are loaded
                            if (selectedClassId) {
                                $('#edit_class_id').val(selectedClassId);
                            }

                            // Populate subjects
                            $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');
                            $.each(response.subjects, function (key, subjectData) {
                                $('#edit_subject_id').append('<option value="' + subjectData.id + '">' + subjectData.name + '</option>');
                            });
                            // Set the selected subject after all options are loaded
                            if (selectedSubjectId) {
                                $('#edit_subject_id').val(selectedSubjectId);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching classes/subjects for edit modal:", xhr);
                    }
                });
            }

            // --- Event handler for opening the EDIT modal ---
            $(document).on('click', '.editMarksBtn', function () {
              let url = $(this).data('url'); // Already includes correct ID
              // console.log("Request URL:", url);
              // Clear and prepare modal
              $('#editMarksForm')[0].reset();

              $.ajax({
                  url: url,
                  type: 'GET',
                  success: function (response) {
                      // Assuming response returns mark data

                      // Example: fill form fields with response
                      $('#edit_mark_id').val(response.data.id);
                      $('#edit_session_id').val(response.data.session_id);
                      resetEditModalFields(response.data.session_id,response.data.student_id, response.data.class_id, response.data.subject_id);
                      // $('#edit_student_id').val(response.data.student_id);
                     
                      // $('#edit_class_id').val(response.data.class_id);
                      // $('#edit_subject_id').val(response.data.subject_id);
                      $('#edit_mid_term_out_off').val(response.data.mid_term_out_off);
                      $('#edit_mid_term_stu_marks').val(response.data.mid_term_stu_marks);
                      $('#edit_final_exam_out_off').val(response.data.final_exam_out_off);
                      $('#edit_final_exam_stu_marks').val(response.data.final_exam_stu_marks);
                      
                      // Show modal
                      $('#editMarksModal').modal('show');

                      // You can also update dropdowns if dynamic using trigger('change')
                  },
                  error: function (xhr) {
                      console.error('Error fetching data', xhr);
                      alert('Failed to load data. Please try again.');
                      $('#editMarksModal').modal('hide');
                  }
              });
            });


          // --- Change event listener for Session in EDIT modal ---
          $('#edit_session_id').on('change', function() {
              let sessionId = $(this).val();
              let selectedStudentId = null;
              resetEditModalFields(sessionId,selectedStudentId);
          });
          function resetEditModalFields(sessionId = null, selectedStudentId = null, selectedClassId = null, selectedSubjectId = null) {
              // Clear previously selected student, class, and subject when session changes
                $('#edit_student_id').empty().append('<option value="">Select Student</option>');
                $('#edit_class_id').empty().append('<option value="">Select Class</option>');
                $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');

                // Reset marks
                $('#edit_mid_term_out_off').val('');
                $('#edit_mid_term_stu_marks').val('');
                $('#edit_final_exam_out_off').val('');
                $('#edit_final_exam_stu_marks').val('');
              if (sessionId) {
                  loadEditStudents(sessionId, selectedStudentId, selectedClassId, selectedSubjectId);
              }
              // No need to call loadEditClassesAndSubjects here directly, it will be handled by student change
          }

          // --- Change event listener for Student in EDIT modal ---
          $('#edit_student_id').on('change', function() {
              let sessionId = $('#edit_session_id').val();
              let studentId = $(this).val();

              // Clear existing marks fields

              $('#edit_mid_term_out_off').val('');
              $('#edit_mid_term_stu_marks').val('');

              $('#edit_final_exam_out_off').val('');
              $('#edit_final_exam_stu_marks').val('');

              // Preselect existing values if they were already selected before
              let selectedClassId  = $('#edit_class_id').val();
              let selectedSubjectId = $('#edit_subject_id').val();
              // Pass null for selectedClassId and selectedSubjectId as these are manual changes
              loadEditClassesAndSubjects(sessionId, studentId, selectedClassId, selectedSubjectId);
          });


        });

        $(document).ready(function () {
            $('#editMarksButton').on('click', function (e) {
                e.preventDefault();

                let form = $('#editMarksForm')[0];
                let formData = new FormData(form);


                $('#formAlert1')
                    .addClass('d-none')
                    .removeClass('alert-danger alert-success')
                    .text('');

                $('.text-danger').text('');
                $('input, select').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('admin.student-marks.update') }}", 
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            $('#formAlert1')
                                .removeClass('d-none alert-danger')
                                .addClass('alert alert-success')
                                .text(response.message);

                            $('#editMarksForm')[0].reset();
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        }
                    },
                    error: function (xhr) {
                        $('#formAlert1')
                            .removeClass('d-none alert-success')
                            .addClass('alert alert-danger')
                            .text('');

                        $('.text-danger').text('');
                        $('input, select').removeClass('is-invalid');

                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;

                            if (response.message) {
                                $('#formAlert1').removeClass('d-none').text(response.message);
                            }

                            if (response.errors) {
                                $.each(response.errors, function (key, value) {
                                    $('#error_' + key).text(value[0]);
                                    $('#' + key).addClass('is-invalid');
                                });
                            }
                        } else {
                            $('#formAlert1').html('An unexpected error occurred.');
                        }
                    }

                });
            });
        });


        function deleteMark(userId) {
          Swal.fire({
              icon: 'warning',
              title: "Are you sure you want to delete this?",
              text: "You won't be able to revert this!",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Delete",
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      url: "{{ route('admin.student-marks.delete')}}",
                      type: 'POST',
                      data: {
                          "id": userId,
                          "_token": '{{ csrf_token() }}',
                      },
                      success: function (data){
                          if (data.status != 200) {
                              toastFire('error', data.message);
                          } else {
                              toastFire('success', data.message);
                              location.reload();
                          }
                      }
                  });
              }
          });
        }     
    </script>
{{-- @endsection --}}
    