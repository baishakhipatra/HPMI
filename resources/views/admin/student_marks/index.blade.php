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
                            <td>{{ $mark->student->student_name ?? '-' }}</td>
                            <td>{{ $mark->class->class ?? '-' }}</td>
                            <td>{{ $mark->subjectlist->sub_name ?? '-' }}</td>
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
                                <a href="" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
