<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Marks - List')

@section('content')

{{-- @if($errors->any())
  <div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
    </ul>
  </div>
@endif --}}


<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Marks Management</h3>
            <small class="text-muted">Manage student marks and assessments</small>
        </div>
        <div>
            <a href="" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Export Data">
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
                    <h4>150</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="ri-percent-line" style="font-size: 40px; color: #1cc88a;"></i>
                    <h5 class="card-title">Average Percentage</h5>
                    <h4>85%</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <input type="text" name="search" class="form-control" placeholder="Search students...">
        </div>
        <div class="form-floating form-floating-outline col-md-4">
            <select name="classes" class="form-select">
                <option value="">All Classes</option>
                @foreach($classOptions as $class)
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-floating form-floating-outline col-md-4 mb-2">
            <select name="subjects" class="form-select">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
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
                    @foreach($marks as $mark)
                        <tr>
                            <td class="mark-student-name">{{ $mark->student->student_name ?? '-' }}</td>
                            <td class="mark-class-name">{{ $mark->class->class ?? '-' }}</td>
                            <td class="mark-subject-name">{{ $mark->subjectlist->sub_name ?? '-' }}</td>
                            <td>{{ $mark->term_one_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->mid_term_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->term_two_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->final_exam_stu_marks ?? '-' }}</td>
                            <td>
                                <strong>
                                    {{
                                        ($mark->term_one_stu_marks ?? 0) +
                                        ($mark->term_two_stu_marks ?? 0) +
                                        ($mark->mid_term_stu_marks ?? 0) +
                                        ($mark->final_exam_stu_marks ?? 0)
                                    }}
                                </strong>
                            </td>
                            <td>
                                {{-- <span class="badge {{ calculateGrade(($mark->term_one_stu_marks ?? 0) + ($mark->term_two_stu_marks ?? 0) + ($mark->mid_term_stu_marks ?? 0) + ($mark->final_exam_stu_marks ?? 0)) == 'F' ? 'bg-danger' : 'bg-success' }}">
                                    {{ calculateGrade(($mark->term_one_stu_marks ?? 0) + ($mark->term_two_stu_marks ?? 0) + ($mark->mid_term_stu_marks ?? 0) + ($mark->final_exam_stu_marks ?? 0)) }}
                                </span> --}}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="#" title="Edit" data-bs-toggle="tooltip">
                                            <i class="ri-pencil-line me-1"></i> Edit
                                        </a> --}}
                                        {{-- <button type="button" class="dropdown-item editMarksBtn"
                                            data-id="{{ $mark->id }}"
                                            data-session-id="{{ $mark->studentAdmission->session_id }}"
                                            data-student-id="{{ $mark->studentAdmission->student_id }}"
                                            data-class-id="{{ $mark->studentAdmission->class_id }}"
                                            data-subject-id="{{ $mark->subject_id }}"
                                            data-term-one-out-off="{{ $mark->term_one_out_off }}"
                                            data-term-one-stu-marks="{{ $mark->term_one_stu_marks }}"
                                            data-term-two-out-off="{{ $mark->term_two_out_off }}"
                                            data-term-two-stu-marks="{{ $mark->term_two_stu_marks }}"
                                            data-mid-term-out-off="{{ $mark->mid_term_out_off }}"
                                            data-mid-term-stu-marks="{{ $mark->mid_term_stu_marks }}"
                                            data-final-exam-out-off="{{ $mark->final_exam_out_off }}"
                                            data-final-exam-stu-marks="{{ $mark->final_exam_stu_marks }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editMarksModal">
                                            Edit
                                        </button> --}}
                                        <button type="button" class="dropdown-item editMarksBtn"
                                            data-id="{{ $mark->id }}"
                                            data-session-id="{{ $mark->studentAdmission->session_id ?? '' }}" {{-- Ensure this is accessible --}}
                                            data-student-id="{{ $mark->studentAdmission->student_id ?? '' }}"
                                            data-student-name="{{ $mark->studentAdmission->student->name ?? '' }}"
                                            data-class-id="{{ $mark->studentAdmission->class_id ?? '' }}"
                                            data-class-name="{{ $mark->studentAdmission->class->class_name ?? '' }}"
                                            data-subject-id="{{ $mark->subject_id ?? '' }}"
                                            data-subject-name="{{ $mark->subject->subject_name ?? '' }}"
                                            data-term-one-out-off="{{ $mark->term_one_out_off }}"
                                            data-term-one-stu-marks="{{ $mark->term_one_stu_marks }}"
                                            {{-- ... and so on for other marks fields --}}
                                            data-bs-toggle="modal" data-bs-target="#editMarksModal">
                                            Edit
                                        </button>

                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="tooltip" title="Delete" onclick="">
                                            <i class="ri-delete-bin-6-line me-1"></i> Delete
                                        </a>
                                    </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($marks->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center text-muted">No records found</td>
                        </tr>
                    @endif
                </tbody>

                
                {{-- for add marks --}}
                <div class="modal fade" id="addMarksModal" tabindex="-1" aria-labelledby="addMarksModalLabel"           aria-hidden="true">
                    <div class="modal-dialog modal-xl">

                        <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="addMarksModalLabel">Add New Marks</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{route('admin.student-marks.store')}}" method="POST">
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
                            </div>

                            <div class="form-floating form-floating-outline col-md-6">
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                </select>
                                <label for="student_id" class="form-label">Student</label>
                            </div>


                            <div class="form-floating form-floating-outline col-md-2">
                                <select name="class_id" id="class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                </select>
                                <label for="class_id" class="form-label">Class</label>
                            </div>

                            
                            <div class="form-floating form-floating-outline col-md-2">
                                <select name="subject_id" id="subject_id" class="form-select" >
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                                    @endforeach
                                </select>
                                <label for="subject_id" class="form-label">Subject</label>
                            </div>

                            <div class="form-floating form-floating-outline col-md-3">
                                <select name="term_one_out_off" id="term_one_out_off" class="form-select">
                                    <option value="">Select</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                    <option value="60">60</option>
                                    <option value="65">65</option>
                                    <option value="70">70</option>
                                    <option value="75">75</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="95">95</option>
                                    <option value="100">100</option>
                                </select>
                                <label for="term_one_out_off" class="form-label">Term 1 Out Of</label>
                            </div>

                            
                            <div class="form-floating form-floating-outline col-md-3">
                                <input type="number" name="term_one_stu_marks" id="term_one_stu_marks" class="form-control" placeholder="Enter marks" >
                                <label for="term_one_stu_marks">Term 1 Student Marks</label>
                            </div>

                        
                            <div class="form-floating form-floating-outline col-md-3">
                                <select name="term_two_out_off" id="term_two_out_off" class="form-select">
                                    <option value="">Select</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                    <option value="60">60</option>
                                    <option value="65">65</option>
                                    <option value="70">70</option>
                                    <option value="75">75</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="95">95</option>
                                    <option value="100">100</option>
                                </select>
                                <label for="term_two_out_off" class="form-label">Term 2 Out Of</label>
                            </div>

                    
                            <div class="form-floating form-floating-outline col-md-3">
                                <input type="number" name="term_two_stu_marks" id="term_two_stu_marks" class="form-control" placeholder="Enter marks">
                                <label for="term_two_stu_marks">Term 2 Student Marks</label>
                            </div>

                            <div class="form-floating form-floating-outline col-md-3">
                                <select name="mid_term_out_off" id="mid_term_out_off" class="form-select" >
                                    <option value="">Select</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                    <option value="60">60</option>
                                    <option value="65">65</option>
                                    <option value="70">70</option>
                                    <option value="75">75</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="95">95</option>
                                    <option value="100">100</option>
                                </select>
                                <label for="mid_term_out_off" class="form-label">Mid Term Out Of</label>
                            </div>

                        
                            <div class="form-floating form-floating-outline col-md-3">
                                <input type="number" name="mid_term_stu_marks" id="mid_term_stu_marks" class="form-control" placeholder="Enter marks" >
                                <label for="mid_term_stu_marks">Mid Term Student Marks</label>
                            </div>

                            
                            <div class="form-floating form-floating-outline col-md-3">
                                <select name="final_exam_out_off" id="final_exam_out_off" class="form-select">
                                    <option value="">Select</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                    <option value="60">60</option>
                                    <option value="65">65</option>
                                    <option value="70">70</option>
                                    <option value="75">75</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                    <option value="90">90</option>
                                    <option value="95">95</option>
                                    <option value="100">100</option>
                                </select>
                                <label for="final_exam_out_off" class="form-label">Final Exam Out Of</label>
                            </div>

                        
                            <div class="form-floating form-floating-outline col-md-3">
                                <input type="number" name="final_exam_stu_marks" id="final_exam_stu_marks" class="form-control" placeholder="Enter marks" >
                                <label for="final_exam_stu_marks">Final Exam Student Marks</label>
                            </div>

                            </div>
                            </div>

                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Marks</button>
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
                                            <select name="student_id" id="edit_student_id" class="form-select" required>
                                                <option value="">Select Student</option>
                                                {{-- Students will be loaded here via AJAX --}}
                                            </select>
                                            <label for="edit_student_id" class="form-label">Student</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-2">
                                            <select name="class_id" id="edit_class_id" class="form-select" required>
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
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                                <option value="90">90</option>
                                                <option value="95">95</option>
                                                <option value="100">100</option>
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
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                                <option value="90">90</option>
                                                <option value="95">95</option>
                                                <option value="100">100</option>
                                            </select>
                                            <label for="edit_term_two_out_off" class="form-label">Term 2 Out Of</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <input type="number" name="term_two_stu_marks" id="edit_term_two_stu_marks" class="form-control" placeholder="Enter marks">
                                            <label for="edit_term_two_stu_marks">Term 2 Student Marks</label>
                                        </div>

                                        <div class="form-floating form-floating-outline col-md-3">
                                            <select name="mid_term_out_off" id="edit_mid_term_out_off" class="form-select">
                                                <option value="">Select</option>
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                                <option value="90">90</option>
                                                <option value="95">95</option>
                                                <option value="100">100</option>
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
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                                <option value="90">90</option>
                                                <option value="95">95</option>
                                                <option value="100">100</option>
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
            </table>
        </div>
    </div>

</div>
  {{-- $('#class_id').empty().append('<option value="">Select Class</option>');
                            $.each(response.classes, function(key, classItem) {
                                $('#class_id').append('<option value="'+classItem.id+'">'+classItem.name+'</option>');
                            }); --}}

@endsection
<script>
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

</script>
<script>
    $(document).ready(function() {
        // Function to load students for the edit modal
        function loadEditStudents(sessionId, selectedStudentId = null) {
            if (!sessionId) {
                $('#edit_student_id').empty().append('<option value="">Select Student</option>');
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
                        $('#edit_student_id').trigger('change'); // Trigger change to load classes/subjects
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

            // Set Session and trigger change to load students
            $('#edit_session_id').val(sessionId);
            loadEditStudents(sessionId, studentId); // Pass the selected student ID

            // Load classes and subjects using the initially retrieved session and student IDs
            // The loadEditStudents will trigger a change event on #edit_student_id which will
            // then call loadEditClassesAndSubjects. So, we don't need to call it directly here.

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
            loadEditStudents(sessionId); // No initial student selected here, as it's a manual change
            // Also clear class and subject if session changes, they'll be reloaded by student change
            $('#edit_class_id').empty().append('<option value="">Select Class</option>');
            $('#edit_subject_id').empty().append('<option value="">Select Subject</option>');
        });

        // --- Change event listener for Student in EDIT modal ---
        $('#edit_student_id').on('change', function() {
            let sessionId = $('#edit_session_id').val();
            let studentId = $(this).val();
            // Pass null for selectedClassId and selectedSubjectId as these are manual changes
            loadEditClassesAndSubjects(sessionId, studentId);
        });

        // Your existing add modal scripts (if any)
        // ... (your existing $('#session_id').on('change') and $('#student_id').on('change') for the ADD modal) ...

    });
</script>
