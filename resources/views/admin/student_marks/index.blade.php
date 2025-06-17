<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Marks - List')

@section('content')

@if($errors->has('term_required'))
    <div class="alert alert-danger">
        {{ $errors->first('term_required') }}
    </div>
@endif

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
            <input type="text" id="search-student" class="form-control" placeholder="Search by student name...">
        </div>
        <div class="form-floating form-floating-outline col-md-4">
            <select id="filter-class" class="form-select">
                <option value="">All Classes</option>
                @foreach($classOptions as $class)
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-floating form-floating-outline col-md-4 mb-2">
            <select id="filter-subject" class="form-select">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="marks-table">
        @include('admin.student_marks.partials.table', ['marks' => $marks])
    </div>
  
</div>
  @if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var myModal = new bootstrap.Modal(document.getElementById('addMarksModal'));
            myModal.show();
        });
    </script>
  @endif

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

    $(document).ready(function() {
        $('#search-student').on('keyup', function() {
            var query = $(this).val();

            $.ajax({
                url: "{{ route('admin.studentmarklist') }}", 
                type: "GET",
                data: { query: query },
                success: function(data) {
                    $('#marks-table').html(data.view);
                }
            });
        });
    });

</script>
