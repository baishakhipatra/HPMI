<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Marks - List')

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

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="ri-graduation-cap-line" style="font-size: 40px; color: #4e73df;"></i>
                        <h5 class="card-title">Total Records</h5>
                        <h4>{{ $totalRecords }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <i class="ri-percent-line" style="font-size: 40px; color: #1cc88a;"></i>
                        <h5 class="card-title">Average Percentage</h5>
                        <h4>{{ $averagePercentage }}%</h4>
                    </div>
                </div>
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
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->sub_name }}</option>
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
                @php
                    // Group strictly by student_id + session_id + class_id (not by names)
                    $groupedMarks = $marks->groupBy(function($item) {
                        return $item->studentAdmission->student_id . '_' .
                            $item->studentAdmission->session_id . '_' .
                            $item->studentAdmission->class_id;
                    });
                @endphp

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
                                Session: {{ $session }} | Class: {{ $class }} | Student: {{ ucwords($student) }}
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered marks-table mt-2">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Term 1</th>
                                            <th>Midterm</th>
                                            <th>Term 2</th>
                                            <th>Final</th>
                                            <th>Total</th>
                                            <th>Grade</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentMarks as $mark)
                                            @php
                                                $total = ($mark->term_one_stu_marks ?? 0) + ($mark->term_two_stu_marks ?? 0) + ($mark->mid_term_stu_marks ?? 0) + ($mark->final_exam_stu_marks ?? 0);
                                                $grade = calculateGrade($total);
                                            @endphp
                                            <tr>
                                                <td>{{ ucwords($mark->subjectlist->sub_name ?? '-') }}</td>
                                                <td>{{ $mark->term_one_out_off.'/'.$mark->term_one_stu_marks ?? '-' }}</td>
                                                <td>{{ $mark->mid_term_out_off.'/'.$mark->mid_term_stu_marks ?? '-' }}</td>
                                                <td>{{ $mark->term_two_out_off.'/'.$mark->term_two_stu_marks ?? '-' }}</td>
                                                <td>{{ $mark->final_exam_out_off.'/'.$mark->final_exam_stu_marks ?? '-' }}</td>
                                                <td><strong>{{ $total }}</strong></td>
                                                <td><span class="grade">{{ $grade }}</span></td>
                                                <td class="actions-cell">
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                            <i class="ri-more-2-line"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <button type="button" class="dropdown-item editMarksBtn"
                                                                data-id="{{ $mark->id }}"
                                                                data-session-id="{{ $mark->studentAdmission->session_id ?? '' }}"
                                                                data-student-id="{{ $mark->studentAdmission->student_id ?? '' }}"
                                                                data-student-name="{{ $mark->studentAdmission->student->name ?? '' }}"
                                                                data-class-id="{{ $mark->studentAdmission->class_id ?? '' }}"
                                                                data-class-name="{{ $mark->studentAdmission->class->class_name ?? '' }}"
                                                                data-subject-id="{{ $mark->subject_id ?? '' }}"
                                                                data-subject-name="{{ $mark->subject->subject_name ?? '' }}"
                                                                data-term-one-out-off="{{ $mark->term_one_out_off }}"
                                                                data-term-one-stu-marks="{{ $mark->term_one_stu_marks }}"
                                                                data-term-two-out-off="{{ $mark->term_two_out_off }}"
                                                                data-term-two-stu-marks="{{ $mark->term_two_stu_marks }}"
                                                                data-mid-term-out-off="{{ $mark->mid_term_out_off }}"
                                                                data-mid-term-stu-marks="{{ $mark->mid_term_stu_marks }}"
                                                                data-final-exam-out-off="{{ $mark->final_exam_out_off }}"
                                                                data-final-exam-stu-marks="{{ $mark->final_exam_stu_marks }}"
                                                                data-bs-toggle="modal" data-bs-target="#editMarksModal">
                                                                <i class="ri-pencil-line me-1"></i> Edit
                                                            </button>

                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="deleteMark({{ $mark->id }})">
                                                                <i class="ri-delete-bin-6-line me-1"></i> Delete
                                                            </a>
                                                        </div>
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
                            <div class="form-floating form-floating-outline col-md-2">
                              <select name="session_id" id="session_id" class="form-select">
                                <option value="">Select Session</option>
                                @foreach($sessions as $item)
                                <option value="{{ $item->session_id }}">{{ $item->session->session_name }}</option>
                                @endforeach
                              </select>
                              <label for="session_id" class="form-label">Session</label>
                              <div class="text-danger" id="error_session_id"></div>
                            </div>

                            <div class="form-floating form-floating-outline col-md-6">
                              <select name="student_id" id="student_id" class="form-select">
                                <option value="">Select Student</option>
                              </select>
                              <label for="student_id" class="form-label">Student</label>
                              <div class="text-danger" id="error_student_id"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-2">
                              <select name="class_id" id="class_id" class="form-select">
                                <option value="">Select Class</option>
                              </select>
                              <label for="class_id" class="form-label">Class</label>
                              <div class="text-danger" id="error_class_id"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-2">
                              <select name="subject_id" id="subject_id" class="form-select">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                                @endforeach
                              </select>
                              <label for="subject_id" class="form-label">Subject</label>
                              <div class="text-danger" id="error_subject_id"></div>
                            </div>

                            <div class="form-floating form-floating-outline col-md-3">
                              <select name="term_one_out_off" id="term_one_out_off" class="form-select">
                                <option value="">Select</option>
                                @foreach (range(50, 100, 5) as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                              </select>
                              <label for="term_one_out_off" class="form-label">Term 1 Out Of</label>
                              <div class="text-danger" id="error_term_one_out_off"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <input type="number" name="term_one_stu_marks" id="term_one_stu_marks"
                                class="form-control" placeholder="Enter marks">
                              <label for="term_one_stu_marks">Term 1 Student Marks</label>
                              <div class="text-danger" id="error_term_one_stu_marks"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <select name="term_two_out_off" id="term_two_out_off" class="form-select">
                                <option value="">Select</option>
                                @foreach (range(50, 100, 5) as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                              </select>
                              <label for="term_two_out_off" class="form-label">Term 2 Out Of</label>
                              <div class="text-danger" id="error_term_two_out_off"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <input type="number" name="term_two_stu_marks" id="term_two_stu_marks"
                                class="form-control" placeholder="Enter marks">
                              <label for="term_two_stu_marks">Term 2 Student Marks</label>
                              <div class="text-danger" id="error_term_two_stu_marks"></div>
                            </div>

                            <div class="form-floating form-floating-outline col-md-3">
                              <select name="mid_term_out_off" id="mid_term_out_off" class="form-select">
                                <option value="">Select</option>
                                @foreach (range(50, 100, 5) as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                              </select>
                              <label for="mid_term_out_off" class="form-label">Mid Term Out Of</label>
                              <div class="text-danger" id="error_mid_term_out_off"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <input type="number" name="mid_term_stu_marks" id="mid_term_stu_marks"
                                class="form-control" placeholder="Enter marks">
                              <label for="mid_term_stu_marks">Mid Term Student Marks</label>
                              <div class="text-danger" id="error_mid_term_stu_marks"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <select name="final_exam_out_off" id="final_exam_out_off" class="form-select">
                                <option value="">Select</option>
                                @foreach (range(50, 100, 5) as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                              </select>
                              <label for="final_exam_out_off" class="form-label">Final Exam Out Of</label>
                              <div class="text-danger" id="error_final_exam_out_off"></div>
                            </div>


                            <div class="form-floating form-floating-outline col-md-3">
                              <input type="number" name="final_exam_stu_marks" id="final_exam_stu_marks"
                                class="form-control" placeholder="Enter marks">
                              <label for="final_exam_stu_marks">Final Exam Student Marks</label>
                              <div class="text-danger" id="error_final_exam_stu_marks"></div>
                            </div>

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
                <div class="modal fade" id="editMarksModal" tabindex="-1" aria-labelledby="editMarksModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editMarksModalLabel">Edit Marks</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form action="{{ route('admin.student-marks.update') }}" method="POST">
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
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-6">
                                            <select name="student_id" id="edit_student_id" class="form-select">
                                                <option value="">Select Student</option>
                                                {{-- Students will be loaded here via AJAX --}}
                                            </select>
                                            <label for="edit_student_id" class="form-label">Student</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-2">
                                            <select name="class_id" id="edit_class_id" class="form-select">
                                                <option value="">Select Class</option>
                                                {{-- Classes will be loaded here via AJAX --}}
                                            </select>
                                            <label for="edit_class_id" class="form-label">Class</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-2">
                                            <select name="subject_id" id="edit_subject_id" class="form-select">
                                                <option value="">Select Subject</option>
                                                {{-- Subjects will be loaded here via AJAX --}}
                                            </select>
                                            <label for="edit_subject_id" class="form-label">Subject</label>
                                        </div>

                                        {{-- ... rest of your form fields for marks ... --}}
                                        <div class="form-floating form-floating-outline col-md-3">
                                            <select name="term_one_out_off" id="edit_term_one_out_off" class="form-select">
                                                <option value="">Select</option>
                                                @foreach (range(50, 100, 5) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="edit_term_one_out_off" class="form-label">Term 1 Out Of</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <input type="number" name="term_one_stu_marks" id="edit_term_one_stu_marks" class="form-control" placeholder="Enter marks">
                                            <label for="edit_term_one_stu_marks">Term 1 Student Marks</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <select name="term_two_out_off" id="edit_term_two_out_off" class="form-select">
                                                <option value="">Select</option>
                                                @foreach (range(50, 100, 5) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="edit_term_two_out_off" class="form-label">Term 2 Out Of</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <input type="number" name="term_two_stu_marks" id="edit_term_two_stu_marks" class="form-control" placeholder="Enter marks">
                                            <label for="edit_term_two_stu_marks">Term 2 Student Marks</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <select name="mid_term_out_off" id="edit_mid_term_out_off" class="form-select" >
                                                <option value="">Select</option>
                                                @foreach (range(50, 100, 5) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="edit_mid_term_out_off" class="form-label">Mid Term Out Of</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <input type="number" name="mid_term_stu_marks" id="edit_mid_term_stu_marks" class="form-control" placeholder="Enter marks">
                                            <label for="edit_mid_term_stu_marks">Mid Term Student Marks</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <select name="final_exam_out_off" id="edit_final_exam_out_off" class="form-select">
                                                <option value="">Select</option>
                                                @foreach (range(50, 100, 5) as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <label for="edit_final_exam_out_off" class="form-label">Final Exam Out Of</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <input type="number" name="final_exam_stu_marks" id="edit_final_exam_stu_marks" class="form-control" placeholder="Enter marks">
                                            <label for="edit_final_exam_stu_marks">Final Exam Student Marks</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update Marks</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
{{-- @section('scripts') --}}
    <script>
        $(document).ready(function () {
            $('#StudentMarksStoreButton').on('click', function (e) {
                e.preventDefault(); 

                // Get the form element
                var form = $('#StudentMarksStore')[0];

                // Create FormData from the form
                var formData = new FormData(form);
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
                             setTimeout(function () {
                                window.location.reload();
                            }, 2000);
                        }
                    },

                    error: function (xhr) {
                        $('#formAlert')
                            .removeClass('d-none alert-success')
                            .addClass('alert alert-danger');

                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;


                            if (response.message) {
                                $('#formAlert').html(response.message);
                            }


                            if (response.errors) {
                                let errorHtml = '';
                                $.each(response.errors, function (key, value) {
                                    errorHtml += `<div>${value}</div>`;

                                    $('#error_' + key).text(value);
                                    $('#' + key).addClass('is-invalid');
                                });

                                $('#formAlert').html(errorHtml);
                            }
                        } else {
                            $('#formAlert').html('An unexpected error occurred. Please try again.');
                        }
                    }

                });
            });
        });

        $('input, select').on('input change', function () {
            var field = $(this).attr('id');
            $('#error_' + field).text('');
            $(this).removeClass('is-invalid');
        });

        $(document).ready(function() {
            $('#session_id').on('change', function() {
                var sessionId = $(this).val();
                $('#student_id').html('<option value="">Loading...</option>');
                if (sessionId) {
                    $.ajax({
                        url: "{{ route('admin.get-students-by-session') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: { sessionId: sessionId },
                        success: function(response) {
                            if(response.success){
                                $('#student_id').empty();
                                $('#student_id').append('<option value="">Select Student</option>');
                                $.each(response.students, function(key, student) {
                                    $('#student_id').append('<option value="'+student.id+'">'+student.name+'</option>');
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                } else {
                    $('#student_id').html('<option value="">Select Student</option>');
                }
            });
            $('#student_id').on('change', function() {
                var student_id = $(this).val();
                var session_id = $('#session_id').val();
                if (session_id && student_id) {
                    $.ajax({
                        url: "{{ route('admin.get-class-by-session-and-student') }}",
                        type: 'GET',
                        dataType: 'json',
                        data: { student_id: student_id,session_id:session_id },
                        success: function(response) {
                            if(response.success){
                                $('#class_id').empty();
                                $.each(response.classes, function(key, classData) {
                                    $('#class_id').append('<option value="'+classData.id+'" selected>'+classData.name+'</option>');
                                });
                                $('#subject_id').empty();
                                $.each(response.subjects, function(key, subjectData) {
                                    
                                    $('#subject_id').append('<option value="'+subjectData.id+'" selected>'+subjectData.name+'</option>');
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                } else {
                    $('#class_id').html('<option value="">Select class</option>');
                    $('#subject_id').html('<option value="">Select subject</option>');
                }
            });
        });

        $(document).ready(function() {
            // Function to load students for the edit modal
            function loadEditStudents(sessionId, selectedStudentId = null) {
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
                                $('#edit_student_id').val(selectedStudentId);
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
                let btn = $(this);

                let markId = btn.data('id');
                let sessionId = btn.data('session-id');
                let studentId = btn.data('student-id');
                let classId = btn.data('class-id');
                let subjectId = btn.data('subject-id');

                // Set mark id in hidden input
                $('#edit_mark_id').val(markId);

                // Set Session
                $('#edit_session_id').val(sessionId);

                // Load students and pre-select the student
                loadEditStudents(sessionId, studentId);

                // Load classes and subjects and pre-select them
                // This is crucial: call loadEditClassesAndSubjects directly here with all initial IDs
                loadEditClassesAndSubjects(sessionId, studentId, classId, subjectId);

                // Set marks values (these don't require AJAX for their values)
                $('#edit_term_one_out_off').val(btn.data('term-one-out-off'));
                $('#edit_term_one_stu_marks').val(btn.data('term-one-stu-marks'));

                $('#edit_term_two_out_off').val(btn.data('term-two-out-off'));
                $('#edit_term_two_stu_marks').val(btn.data('term-two-stu-marks'));

                $('#edit_mid_term_out_off').val(btn.data('mid-term-out-off'));
                $('#edit_mid_term_stu_marks').val(btn.data('mid-term-stu-marks'));

                $('#edit_final_exam_out_off').val(btn.data('final-exam-out-off'));
                $('#edit_final_exam_stu_marks').val(btn.data('final-exam-stu-marks'));
            });

            // --- Change event listener for Session in EDIT modal ---
            $('#edit_session_id').on('change', function() {
                let sessionId = $(this).val();
                // Clear previously selected student, class, and subject when session changes
                $('#edit_student_id').empty().append('<option value="">Select Student</option>');
                $('#edit_class_id').empty().append('<option value="">Select Class</option>');
                $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');

                // Clear existing marks fields
                $('#edit_term_one_out_off').val('');
                $('#edit_term_one_stu_marks').val('');

                $('#edit_term_two_out_off').val('');
                $('#edit_term_two_stu_marks').val('');

                $('#edit_mid_term_out_off').val('');
                $('#edit_mid_term_stu_marks').val('');

                $('#edit_final_exam_out_off').val('');
                $('#edit_final_exam_stu_marks').val('');

                loadEditStudents(sessionId); // No initial student selected here, as it's a manual change
                // No need to call loadEditClassesAndSubjects here directly, it will be handled by student change
            });

            // --- Change event listener for Student in EDIT modal ---
            $('#edit_student_id').on('change', function() {
                let sessionId = $('#edit_session_id').val();
                let studentId = $(this).val();

                // Clear existing marks fields
                $('#edit_term_one_out_off').val('');
                $('#edit_term_one_stu_marks').val('');

                $('#edit_term_two_out_off').val('');
                $('#edit_term_two_stu_marks').val('');

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

            // Your existing add modal scripts (if any)
            // ... (your existing $('#session_id').on('change') and $('#student_id').on('change') for the ADD modal) ...

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
                    /* Read more about isConfirmed, isDenied below */
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
    
